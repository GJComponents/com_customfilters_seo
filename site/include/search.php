<?php
/**
 * @package customfilters
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright    Copyright (C) 2012-2021 breakDesigns.net. All rights reserved
 * @license    GNU/GPL v2
 */
defined('JPATH_BASE') or die();

use \Joomla\String\StringHelper;
use Joomla\CMS\Factory as JFactory;

/**
 * Class CfSearchHelper
 */
class CfSearchHelper
{
    /**
     *
     * @var string
     * @since 2.2.0
     */
    protected $currentLangPrefix;

    /**
     *
     * @var string
     * @since 2.2.0
     */
    protected $defaultLangPrefix;

    /**
     * @var array
     * @since 2.2.0
     */
    protected $tokens;

    /**
     *
     * @var  array
     * @todo remove stopwords from the query
     * @since 2.2.0
     */
    private $commonWords = array(
        'OR',
        'AND'
    );

    /**
     * Class constructor
     * @since 2.2.0
     */
    public function __construct()
    {
        $this->currentLangPrefix = \cftools::getCurrentLanguagePrefix();
        $this->defaultLangPrefix = \cftools::getDefaultLanguagePrefix();
    }

    /**
     * Method to tokenize a text string.
     *
     * @param string $input
     *            The input to tokenize.
     * @param string $lang
     *            The language of the input.
     * @param boolean $matching
     *            Flag to indicate the type of matching [optional]
     *
     * @return array An array of FinderIndexerToken objects.
     * @author Sakis Terz
     * @since 2.2.0
     */
    public function tokenize($input, $lang, $matching = 'any')
    {
        static $cache;
        $input = $this->formatInput($input);
        $store = StringHelper::strlen($input) < 128 ? md5($input . '::' . $lang . '::' . $matching) : null;

        // Check if the string has been tokenized already.
        if ($store && isset($cache[$store])) {
            return $cache[$store];
        }

        // create identicals. e.g. 100 gr ,100gr
        $generate_identicals = false;
        $componentParams = \cftools::getComponentparams();
        $searchfields = $componentParams->get('keyword_searchfield', array(
            'l.product_name',
            'l.product_s_desc',
            'catlang.category_name',
            'mflang.mf_name',
            'custom'
        ), 'array');

        /*
         * Convert html entities to their utf-8 equivalent
         * Remove whitespaces at start and end
         * Remove multiple space characters and replaces with a single space.
         */
        $input = html_entity_decode($input, ENT_QUOTES, 'UTF-8');
        $input = StringHelper::trim($input);
        $input = preg_replace('#\s+#mi', ' ', $input);
        $terms = explode(' ', $input);
        $primary_tokens = array();

        /*
         * Create the single word tokens
         */
        for ($i = 0; $i < count($terms); $i++) {
            $token = new stdClass();
            $token->term = StringHelper::trim($terms[$i]);
            $token->phrase = false;
            $token->numerical = preg_match('/^-?[0-9]+(\.[0-9]+)?$/', $token->term);

            /*
             * check if the number is followed by a unit
             * Units are usually 1-2 characters BUT NOT NUMBERS
             * In that case unit should not be a new token but part of this one
             */
            if ($token->numerical && isset($terms[$i + 1]) && strlen($terms[$i + 1]) <= 2 && preg_match('/^-?[0-9]+?$/', $terms[$i + 1]) == 0) {
                $tmp_term = $token->term;
                $token->term = $tmp_term . ' ' . $terms[$i + 1];

                // create 1 more without the space
                if ($generate_identicals) {
                    $token_tmp = new stdClass();
                    $token_tmp->term = $tmp_term . $terms[$i + 1];
                    $token_tmp->phrase = false;
                    $token_tmp->numerical = true;
                    $token->identical = $token_tmp;
                }
                $i++;
            }
            // Add the token to the stack.
            $primary_tokens[] = $token;
        }

        // check if any of the tokens is category/manufacturer/customfield value
        $foundInDb = array();
        if (in_array('catlang.category_name', $searchfields)) {
            $categories = $this->getCategories($primary_tokens);
            if (!empty($categories)) {
                $foundInDb['category'] = $categories;
            }
        }

        if (in_array('mflang.mf_name', $searchfields)) {
            $manufacturers = $this->getManufacturers($primary_tokens);
            if (!empty($manufacturers)) {
                $foundInDb['manufacturer'] = $manufacturers;
            }
        }

        if (in_array('custom', $searchfields)) {
            $customfields = $this->getCustomfieldValues($primary_tokens);
            if (!empty($customfields)) {
                $foundInDb['customvalue'] = $customfields;
            }
        }

        $spacer = ' ';
        $n = count($primary_tokens) - 1;
        $phrases = array();

        /*
         * Create the combinations
         * The rule is that the phrase should have as much words as it initialy had
         */
        if ($matching == 'any' && count($primary_tokens) < 4) {
            foreach ($primary_tokens as $i => $ptoken) {
                $phrase_parts = array();
                $identical_phrase_parts = array();

                if ($i < $n)
                    $j = $i + 1;
                else
                    $j = 0;
                while ($j != $i) {

                    // an identical found or exists
                    if (!empty($identical_phrase_parts) || !empty($primary_tokens[$j]->identical)) {

                        // if current is no identical but exists identical, use the main term
                        $term = !empty($primary_tokens[$j]->identical) ? $primary_tokens[$j]->identical->term : $primary_tokens[$j]->term;
                        $identical_phrase_parts = $phrase_parts;
                        $identical_phrase_parts[] = $term;
                    }
                    $phrase_parts[] = $primary_tokens[$j]->term;

                    if ($j < $n)
                        $j++;
                    else
                        $j = 0;
                }

                // no identical

                $phrases[] = StringHelper::trim($ptoken->term . $spacer . implode($spacer, $phrase_parts));

                // 1st word identical
                if (!empty($ptoken->identical)) {
                    $phrases[] = StringHelper::trim($ptoken->identical->term . $spacer . implode($spacer, $phrase_parts));
                }
                // rest identical
                if (!empty($identical_phrase_parts)) {
                    $phrases[] = StringHelper::trim($ptoken->term . $spacer . implode($spacer, $identical_phrase_parts));
                }

                // in case the phrase has more than 2 terms, create combinations reversing it
                if ($n >= 2) {
                    // no identical

                    $phrases[] = StringHelper::trim($ptoken->term . $spacer . implode($spacer, array_reverse($phrase_parts)));

                    // 1st word identical
                    if (!empty($ptoken->identical)) {
                        $phrases[] = StringHelper::trim($ptoken->identical->term . $spacer . implode($spacer, array_reverse($phrase_parts)));
                    }
                    // rest identical
                    if (!empty($identical_phrase_parts)) {
                        $phrases[] = StringHelper::trim($ptoken->term . $spacer . implode($spacer, array_reverse($identical_phrase_parts)));
                    }
                }
            }
        } // absolute matching - no surprises - just check the input phrase
        else {
            $phrase_parts = array();
            foreach ($primary_tokens as $ptoken) {
                $phrase_parts[] = $ptoken->term;
            }
            $phrases[] = implode($spacer, $phrase_parts);
        }

        $this->tokens = $this->generateTokens($phrases, $foundInDb);

        if ($store) {
            $cache[$store] = $this->tokens;
        }

        return $this->tokens;
    }

    /**
     * Format the input accordingly
     *
     * @param $input
     * @return bool|string
     * @since 2.2.0
     */
    protected function formatInput($input)
    {
        $inputInit = StringHelper::trim($input);
        $input = function_exists('mb_strtolower') ? mb_strtolower($input) : StringHelper::strtolower($input);
        $input = $input == false ? $inputInit : $input;
        return $input;
    }

    /**
     * Function that generates the token objects
     *
     * @param array $inputs strings or token objects
     * @param array $foundInDb the records found in the db based on the input (category/manufacturer/custom values)
     *
     * @return array the new tokens
     *
     * @since 2.2.0
     */
    protected function generateTokens($inputs, $foundInDb)
    {
        static $tokens = array();
        static $level = 0;
        static $number_of_tokens = 0;

        $level++;
        /*
         * the $max_level defines how many times that function will be executed
         * including the times that the subtractFromPhrase will be executed to generate new tokens from the db
         * the $max_level defines the depth of the analysis as tokens are being analyzed if they contain db values again and again
         */
        $max_level = 0;
        /*
         * it is important to have a limit to the max_level (i.e. iterations).
         * Otherwise the function can be manipulated by adding more keywords found in the database (e.g. categories, custom field values).
         * This will lead to the applicaton timeout and memory limits
         */
        $max_level_limit = 4;


        foreach ($inputs as $input) {
            if (is_object($input)) {
                $token = $input;
            } // create the object
            else {
                $token = new stdClass();
                $token->term = $input;
                $token->phrase = true;
                $token->weight = 0;
                $token->category = false;
                $token->manufacturer = false;
                $token->customvalue = false;
                $store_id = md5(json_encode($token));
                // the 1st time add it to the stack as is
                $tokens[$store_id] = $token;
            }
            if (empty($token->term)) {
                continue;
            }

            /*
            * If any of the words is category, manufacturer, custom value, then new combinations without that word can be generated
            * add new tokens by substracting categories/manufacturers/custom values..etc
            */
            foreach ($foundInDb as $key => $dbValues) {
                // if the array is empty, go next
                if (empty($dbValues)) {
                    continue;
                }
                if (count($dbValues) > $max_level && count($dbValues) <= $max_level_limit) {
                    $max_level = count($dbValues);
                }

                $new_tokens = $this->subtractFromPhrase($token, $dbValues, $key);
                if (!empty($new_tokens)) {
                    $tokens = array_merge($tokens, $new_tokens);
                }
            }
        }

        /*
         * Recursion.
         *
         * Allowed if we have not reach the max_level
         * and the tokens (after susbtracting) are more than the previous time.
         * If they are the same number as the previous time, means that no more can be produced with subtraction.
         */
        if ($level <= $max_level && count($tokens)>$number_of_tokens) {
            $number_of_tokens = count($tokens);
            $this->generateTokens($tokens, $foundInDb);
        }
        return array_reverse($tokens);
    }

    /**
     * Gets a token and create new substracting terms found in the db (e.g.
     * category, manufacturer)
     *
     * @param object $tokenInit
     * @param array $dbrecords
     * @param string $key
     *
     * @return array an array that contains the new tokens.
     * @since 2.2.0
     */
    protected function subtractFromPhrase($tokenInit, $dbrecords, $key = 'category')
    {
        $new_tokens = [];
        static $new_terms = [];
        $token = clone $tokenInit;

        // add a space at the end to match the regex
        $token->term = $token->term . ' ';

        foreach ($dbrecords as $id => $dbrecord) {
            $canbeplurar = false;

            // strip the last 2 characters, in case plurar is used in the db for storing categories
            if ($key == 'category' && strlen($dbrecord) > 5) {
                $dbrecord = substr($dbrecord, 0, strlen($dbrecord) - 2);
                $canbeplurar = true;
            }

            // search inside the terms for the db records. Do note that they maby be a bit different in the db. e.g. in plurar
            if (strpos($token->term, $dbrecord) !== false) {
                //remove db record from term and create a new token
                if ($canbeplurar == false) {
                    $new_term = str_replace($dbrecord, '', $token->term);
                } else {
                    $new_term = preg_replace('/' . $dbrecord . '([^\x30-\x7F]|[a-zA-Z0-9]){0,2}\s/', '', $token->term);
                }
                $new_term = preg_replace('#\s+#mi', ' ', $new_term);
                $new_term = (string)StringHelper::trim($new_term);
                $store_id = $new_term;
                if (!in_array($store_id, $new_terms)) {
                    $record_obj = unserialize($id);
                    $new_terms[] = $store_id;
                    $new_token = clone $token;
                    $new_token->term = trim($new_term);
                    //the more the database records included in a term, the more the weight
                    $new_token->weight++;
                    $new_token->phrase = true;
                    if (!isset($new_token->{$key})) {
                        $new_token->{$key} = [];
                    }
                    $new_token->{$key}[] = $record_obj;
                    $store_id_token = md5(json_encode($new_token));
                    $new_tokens[$store_id_token] = $new_token;
                }
            }
        }
        return $new_tokens;
    }

    /**
     * Checks if any of the terms is category
     *
     * @param mixed $input
     *            - string or array of strings
     * @return array records from the db matching
     * @since 2.2.0
     */
    public function getCategories($tokens)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('LOWER(cl.category_name) AS name, cl.virtuemart_category_id as id')
            ->from('#__virtuemart_categories_' . $this->currentLangPrefix . ' AS cl')
            ->innerJoin('#__virtuemart_categories AS c ON c.virtuemart_category_id=cl.virtuemart_category_id');

        if (!is_array($tokens)) {
            $query->where('cl.category_name LIKE ' . $db->quote($db->escape($tokens->term, true) . '%', false));
        } else {
            $whereOr = array();
            foreach ($tokens as $token) {
                $whereOr[] = 'cl.category_name LIKE ' . $db->quote($db->escape($token->term, true) . '%', false);
                if (isset($token->identical))
                    $whereOr[] = 'cl.category_name LIKE ' . $db->quote($db->escape($token->identical->term, true) . '%', false);
            }
            $query->where(implode(' OR ', $whereOr));
        }
        $query->where('c.published=1');
        $db->setQuery($query);
        $results = $db->loadObjectList();
        $results = $this->createAssocArrayFromObjectList($results);

        return $results;
    }

    /**
     * Checks if any of the terms is manufacturer
     *
     * @param mixed $input
     *            - string or array of strings
     * @return array records from the db matching
     * @since 2.2.0
     */
    public function getManufacturers($tokens)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('LOWER(ml.mf_name) AS name, ml.virtuemart_manufacturer_id AS id')
            ->from('#__virtuemart_manufacturers_' . $this->currentLangPrefix . ' AS ml')
            ->innerJoin('#__virtuemart_manufacturers AS m ON m.virtuemart_manufacturer_id=ml.virtuemart_manufacturer_id');

        if (!is_array($tokens)) {
            $query->where('ml.mf_name LIKE ' . $db->quote($db->escape($tokens->term, true) . '%', false));
        } else {
            $whereOr = array();
            foreach ($tokens as $token) {
                $whereOr[] = ('ml.mf_name LIKE ' . $db->quote($db->escape($token->term, true) . '%', false));
                if (isset($token->identical))
                    $whereOr[] = ('ml.mf_name LIKE ' . $db->quote($db->escape($token->identical->term, true) . '%', false));
            }
            $query->where(implode(' OR ', $whereOr));
        }
        $query->where('m.published=1');
        $db->setQuery($query);
        $results = $db->loadObjectList();
        $results = $this->createAssocArrayFromObjectList($results);

        return $results;
    }

    /**
     * Checks if any of the terms is custom field value
     *
     * @param mixed $tokens
     *            - string or array of strings
     * @return array records from the db matching
     * @since 2.2.0
     */
    public function getCustomfieldValues($tokens)
    {
        $phrases = array();
        $db = JFactory::getDbo();
        $custom_plg = array();
        $whereOr = array();
        $final_result = array();

        // get also from custom plugins
        $published_cf = \cftools::getCustomFilters();
        foreach ($tokens as $token) {
            $custom_ids = array();
            $custom_ids_plg = array();
            $custom_plg_tmp = array();
            $term_identical_str = null;
            $term_identical_orig = null;

            if (isset($token->identical)) {
                $term_identical_orig = $db->quote($token->identical->term . '%');
            }
            $original_term = $db->quote($token->term . '%');

            foreach ($published_cf as $cf) {

                // not plugin
                if ($cf->field_type != 'E') {
                    $custom_ids[] = $cf->custom_id;
                } // plugin
                else {

                    // if the plugin has not declared the necessary params go to the next
                    if (empty($cf->pluginparams))
                        continue;

                    // get vars from plugins
                    $term_identical = null;
                    $customvalues_table = $cf->pluginparams->customvalues_table;
                    $customvalue_value_field = $cf->pluginparams->customvalue_value_field;
                    $product_customvalues_table = $cf->pluginparams->product_customvalues_table;
                    $sel_field = $cf->pluginparams->filter_by_field;
                    $filter_data_type = $cf->pluginparams->filter_data_type;

                    if (!isset($custom_plg[$customvalues_table])) {
                        $custom_plg[$customvalues_table] = new stdClass();
                        $custom_plg[$customvalues_table]->filter_by_field = $sel_field;
                        $custom_plg[$customvalues_table]->customvalue_value_field = $customvalue_value_field;
                        $custom_plg[$customvalues_table]->customvalues_table = $customvalues_table;
                        $custom_plg[$customvalues_table]->product_customvalues_table = $product_customvalues_table;
                        $custom_plg[$customvalues_table]->custom_ids = array();
                        $custom_plg[$customvalues_table]->whereOr = array();
                    }
                    if (!isset($custom_ids_plg[$customvalues_table]))
                        $custom_ids_plg[$customvalues_table] = array();
                    if (!in_array($cf->custom_id, $custom_ids_plg[$customvalues_table]))
                        $custom_ids_plg[$customvalues_table][] = $cf->custom_id;

                    if (!isset($custom_plg_tmp[$customvalues_table]))
                        $custom_plg_tmp[$customvalues_table] = array();
                    $custom_plg_tmp[$customvalues_table][] = $custom_plg[$customvalues_table];
                }
            }

            // format the native customs where for that token
            if (!empty($custom_ids)) {
                $whereOr[] = '(cfv.customfield_value LIKE ' . $original_term . ' AND virtuemart_custom_id IN (' . implode(',', $custom_ids) . '))';
                if (isset($term_identical_orig))
                    $whereOr[] = '(cfv.customfield_value LIKE ' . $term_identical_orig . ' AND virtuemart_custom_id IN (' . implode(',', $custom_ids) . '))';
            }

            foreach ($custom_plg_tmp as $dbtable => $custom_p) {
                $custom_plg[$dbtable]->whereOr[] = '(' . $custom_plg[$dbtable]->customvalue_value_field . ' LIKE ' . $original_term . ' AND virtuemart_custom_id IN(' . implode(',', $custom_ids_plg[$dbtable]) . '))';
                if (isset($term_identical_orig))
                    $custom_plg[$dbtable]->whereOr[] = '(' . $custom_plg[$dbtable]->customvalue_value_field . ' LIKE ' . $term_identical_orig . ' AND virtuemart_custom_id IN(' . implode(',', $custom_ids_plg[$dbtable]) . '))';
            }
        }

        // form the query for the native customs
        if (!empty($whereOr)) {
            $query = $db->getQuery(true);
            $query->select('DISTINCT LOWER(customfield_value) AS  name, LOWER(customfield_value) AS  value, cfv.virtuemart_custom_id AS custom_id , "#__virtuemart_product_customfields" AS `products_table`,"customfield_value" AS filter_by_field,  0 AS `is_custom`')->from('#__virtuemart_product_customfields AS cfv');
            $query->where(implode(' OR ', $whereOr));
            $db->setQuery($query);
            // print_r((string)$query);
            $results = $db->loadObjectList();
            $final_result = array_merge($final_result, $this->createAssocArrayFromObjectList($results));
        }
        // form the query for the plugin customs
        if (!empty($custom_plg)) {
            foreach ($custom_plg as $plg) {
                $query = $db->getQuery(true);
                $query->select('LOWER(' . $plg->customvalue_value_field . ') AS  name, cfv.' . $plg->filter_by_field . ' AS value, virtuemart_custom_id AS custom_id ,"' . $plg->product_customvalues_table . '" AS `products_table` ,"' . $plg->filter_by_field . '" AS filter_by_field, 1 AS is_custom')->from($plg->customvalues_table . ' AS cfv');
                $query->where(implode(' OR ', $plg->whereOr));
                $db->setQuery($query);
                $results = $db->loadObjectList();
                $final_result = array_merge($final_result, $this->createAssocArrayFromObjectList($results));
            }
        }

        return array_unique($final_result);
    }

    /**
     * Create an assoc array setting keys and values from object properties
     *
     * @param array $array
     * @param string $key
     *            an object property
     * @param string $value
     *            an object property
     * @since 2.2.0
     */
    public function createAssocArrayFromObjectList($array, $key = 'id', $value = 'name')
    {
        $new_array = array();
        if (!is_array($array)) {
            return $array;
        }
        foreach ($array as $obj) {
            $new_key = serialize($obj);
            $new_array[$new_key] = $obj->{$value};
        }

        return $new_array;
    }
}
