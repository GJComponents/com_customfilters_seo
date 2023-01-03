<?php
/**
 * @since       2.3.0
 * @author      Sakis Terz
 * @package     customfilters
 * @copyright   Copyright (C) 2012-2021 breakdesigns.net . All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once JPATH_SITE . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'mod_cf_filtering' . DIRECTORY_SEPARATOR . 'CfFilter.php';

/**
 * Class CfOutput
 *
 * Format the output vars
 * @since 2.2.0
 */
class CfOutput
{
    //the variable which will be escaped
    protected static $cfOutputs = array();

    //indicates if the array of variables is per filter
    protected static $encodedInputs = array();

    // all the outputs are stored here
    public $perfilter;

    //cache for the encoded inputs
    protected $variable;

	/**
	 * Получает вывод (кэшируется)
	 * ---
	 * Gets the output (cached)
	 *
	 * @param   array|string  $variable
	 * @param   bool   $escape
	 * @param   bool   $perfilter
	 *
	 * @return array|string
	 * @since  2.3.0
	 * @author Sakis Terz
	 */
    public static function getOutput( $variable , bool $escape = true, bool $perfilter = false)
    {
        $hash = md5(json_encode($variable) . $escape . $perfilter);
        if (!isset(self::$cfOutputs[$hash])) {
            $cfOutput = new CfOutput();
            $cfOutput->setVariable($variable);
            $cfOutput->setPerfilter($perfilter);
            $cfOutput->setEscape($escape);

            self::$cfOutputs[$hash] = $cfOutput->prepareVariables();
        }

        return self::$cfOutputs[$hash];
    }

    /**
     * Устанавливает переменную в классе
     * ---
     * Sets the variable inthe class
     *
     * @param   string|array  $variable
     * @since    1.0.0
     */
    public function setVariable(  $variable)
    {
        $this->variable = $variable;
    }

    /**
     * Устанавливает перфильтр в классе
     * ---
     * Sets the perfilter inthe class
     *
     * @param mixed $variable
     *
     * @since 2.1
     */
    public function setPerfilter($perfilter)
    {
        $this->perfilter = $perfilter;
    }

    public function setEscape($escape)
    {
        $this->escape = $escape;
    }

    /**
     * Escapes variables for output
     *
     * @return array
     * @since 2.3.0
     */
    public function prepareVariables()
    {
        if (empty($this->variable)) {
            return $this->variable;
        }

        if (!is_array($this->variable)) {
            if ($this->escape) {
                //decode from any other encoding. VM saves some strings e.g. Manufacturers encoded in the db table
                $decoded = htmlspecialchars_decode($this->variable, ENT_COMPAT);
                return htmlspecialchars($decoded, ENT_COMPAT, 'UTF-8');
            }
            return $this->variable;
        }

        $new_array = array();
        foreach ($this->variable as $var_name => $var) {
            if (is_array($var)) {
                $new_array[$var_name] = array();

                // is custom
                if (strpos($var_name, 'custom_f_') !== false && $this->perfilter == false) {
                    $new_array[$var_name] = $this->encodeVar($var, $var_name);
                } else {
                    foreach ($var as $key => $var2) {
                        if (!empty($var2) && !is_array($var2)) {
                            $new_array[$var_name][$key] = $this->escape ? htmlspecialchars($var2, ENT_COMPAT,
                                'UTF-8') : $var2;
                        } // multi-dimensional array possibly holding the filters per perfilter
                        else {
                            $new_array[$var_name][$key] = array();

                            // is custom
                            if (strpos($key, 'custom_f_') !== false) {
                                $new_array[$var_name][$key] = $this->encodeVar($var2, $key);
                            } else {
                                if (is_array($var2)) {
                                    foreach ($var2 as $key2 => $var3) {
                                        $new_array[$var_name][$key][$key2] = $this->escape ? htmlspecialchars($var3,
                                            ENT_COMPAT, 'UTF-8') : $var3;
                                    }
                                }
                            }
                        }
                    }
                }
            } // scalar var
            else {
                $new_array[$var_name] = htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
            }
        }
        return $new_array;
    }

	/**
	 * Some vars such as the custom filters values needs to be encoded
	 * This function is mainly called by the module that needs the values as output to check for the selected values
	 *
	 * @param   array   $array  inputs
	 * @param   string  $var    the name of the var
	 *
	 * @return array output
	 * @throws Exception
	 * @since  2.2.0
	 * @author Sakis Terz
	 */
    public function encodeVar($array, $var_name)
    {
        $store = md5(json_encode($array) . $var_name);

        if (!isset(self::$encodedInputs[$store])) {
            $published_cf = cftools::getCustomFilters();
            $newarray = array();

            if (isset($published_cf)) {

                // find the id of the custom
                preg_match('/[0-9]+/', $var_name, $mathcess);
                if (!empty($mathcess[0])) {
                    $cf = $published_cf[$mathcess[0]];

                    // if not number range or date, encode it
                    if (strpos($cf->disp_type, CfFilter::DISPLAY_INPUT_TEXT) === false
                        && strpos($cf->disp_type, CfFilter::DISPLAY_RANGE_SLIDER) === false
                        && strpos($cf->disp_type, CfFilter::DISPLAY_RANGE_DATES) === false) {
                        $newarray = cftools::bin2hexArray($array);
                    } else {
                        $newarray = $array;
                    }
                }
            }
            self::$encodedInputs[$store] = $newarray;
        }
        return self::$encodedInputs[$store];
    }
}
