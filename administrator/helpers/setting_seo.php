<?php

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

class HelperSetting_seo
{

    /**
     * Создать hash для ID поля
     * @param array $opt_array
     * @return array[]
     * @since    1.0.0
     */
    public function processEncodeOptions(array $opt_array): array
    {
        $new_opt_array = array();
        $i = 1; // it must be >0. 0 is used for the clear (1st option)
        foreach ($opt_array as $op)
        {
            $op->name = trim($op->name);
            if (isset($op->name) && $op->name != '')
            {
                $matches = [];
                // translate only if it can be translated
                preg_match('/^[0-9A-Z_-]+$/i', $op->name, $matches);

                if (!empty($matches[0]))
                {
                    $op->name = Text::_($op->name);
                }
                $op->id = bin2hex(trim($op->id));
                $new_opt_array[$i] = $op;
                $i++;
            }
        }

        return $new_opt_array ;
    }

}