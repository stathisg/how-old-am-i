<?php
/*
Plugin Name: How Old Am I
Plugin URI: http://burnmind.com/freebies/how-old-am-i
Version: 1.2.0
Author: Stathis Goudoulakis
Author URI: http://stathisg.com/
Description: Calculates and displays your age in several formats.

Copyright 2012-2015 Stathis Goudoulakis (email: me@stathisg.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists("howOldAmI"))
{
    class HowOldAmI
    {
        private $adminOptionsTitle = 'HowOldAmIAdminOptions';

        private function getAdminOptions()
        {
            $adminOptions = array('birthDay' => 1,
                                  'birthMonth' => 1,
                                  'birthYear' => 1980,
                                  'calculationType' => 'absolute',
                                  'format' => 'year',
                                  'custom-format' => '',
                                  'negativeAgeEnabled' => false,
                                  'negativeAgeDisplay' => 'symbol',
                                  'negativeAgeCustomDisplay' => '',
                                  'negativeAgePosition' => 0);
            $options = get_option($this->adminOptionsTitle);
            if(!empty($options))
            {
                foreach ($options as $key => $option)
                {
                    $adminOptions[$key] = $option;
                }
            }
            update_option($this->adminOptionsTitle, $adminOptions);
            return $adminOptions;
        }

        function displayAge($atts)
        {
            $options = $this->getAdminOptions();

            extract(shortcode_atts(array(
                'on' => false,
                'bday' => false
            ), $atts));

            $returnValue = '';

            switch($bday)
            {
                case false:
                    $birthDate = new DateTime();
                    $birthDate->setDate($options['birthYear'], $options['birthMonth'], $options['birthDay']);
                    $birthDate->setTime(0, 0);
                    break;
                case 'post':
                    $birthDate = new DateTime();
                    $birthDate->setDate(get_the_date('Y'),get_the_date('m'),get_the_date('d'));
                    $birthDate->setTime(get_the_time('H'), get_the_time('i'));
                    break;
                default:
                    $birthDate = new DateTime($bday . ' 00:00:00');
            }

            switch($on)
            {
                case false:
                    $now = new DateTime("now");
                    break;
                case 'post':
                    $now = new DateTime();
                    $now->setDate(get_the_date('Y'),get_the_date('m'),get_the_date('d'));
                    $now->setTime(get_the_time('H'), get_the_time('i'));
                    break;
                default:
                    $now = new DateTime($on . ' 00:00:00');
            }

            $difference = $birthDate->diff($now);

            $yearsOld = $difference->y;
            $monthsOld = $difference->m;
            $daysOld = $difference->d;
            $totalDaysOld = $difference->days;

            if($options['format']==='year' || $options['format']==='year-words')
            {
                $returnValue = $yearsOld;
                if($options['calculationType']==='relative' && $monthsOld >= 6)
                {
                    $returnValue = $yearsOld + 1;
                }
                if($options['format']==='year-words')
                {
                    require_once('_convert_number_to_words.php');
                    $returnValue = _convert_number_to_words($returnValue);
                }
            }
            else if($options['format']==='custom')
            {
                require_once('_convert_number_to_words.php');
                $returnValue = str_replace('%years%', $yearsOld, $options['custom-format']);
                $returnValue = str_replace('%months%', $monthsOld, $returnValue);
                $returnValue = str_replace('%days%', $daysOld, $returnValue);
                $returnValue = str_replace('%total-days%', $totalDaysOld, $returnValue);
                $returnValue = str_replace('%w-years%', _convert_number_to_words($yearsOld), $returnValue);
                $returnValue = str_replace('%w-months%', _convert_number_to_words($monthsOld), $returnValue);
                $returnValue = str_replace('%w-days%', _convert_number_to_words($daysOld), $returnValue);
                $returnValue = str_replace('%w-total-days%', _convert_number_to_words($totalDaysOld), $returnValue);
                $returnValue = stripslashes($returnValue);
            }
            else
            {
                if($options['format']==='year-month-words')
                {
                    require_once('_convert_number_to_words.php');
                    $yearsOld = _convert_number_to_words($yearsOld);
                    $monthsOld = _convert_number_to_words($monthsOld);
                }
                $returnValue = "$yearsOld years and $monthsOld months";
            }

            if($birthDate > $now && $options['negativeAgeEnabled']) {
                switch ($options['negativeAgeDisplay']) {
                    case 'symbol':
                        $negative_symbol_word = '-';
                        break;
                    
                    case 'word':
                        $negative_symbol_word = 'minus ';
                        break;
                    
                    case 'custom':
                        $negative_symbol_word = stripslashes($options['negativeAgeCustomDisplay']);
                        break;
                    
                    default:
                        $negative_symbol_word = '';
                        break;
                }

                if($options['negativeAgePosition'] === 0) {
                    $returnValue = $negative_symbol_word . $returnValue;
                } else {
                    $returnValue = $returnValue . $negative_symbol_word;
                }
            }
            
            return $returnValue;
        }

        public function printAdminPage()
        {
            $options = $this->getAdminOptions();

            if (isset($_POST['updateHowOldAmI']))
            {
                if (isset($_POST['birthDay']))
                {
                    $options['birthDay'] = intval($_POST['birthDay']);
                }

                if (isset($_POST['birthMonth']))
                {
                    $options['birthMonth'] = intval($_POST['birthMonth']);
                }

                if (isset($_POST['birthYear']))
                {
                    $options['birthYear'] = intval($_POST['birthYear']);
                }

                if (isset($_POST['calculationType']))
                {
                    $options['calculationType'] = $_POST['calculationType'];
                }

                if (isset($_POST['format']))
                {
                    $options['format'] = $_POST['format'];
                }

                if (isset($_POST['custom-format']))
                {
                    $options['custom-format'] = $_POST['custom-format'];
                }

                if (empty($_POST['negative-display-status']))
                {
                    $options['negativeAgeEnabled'] = false;
                }
                else
                {
                    $options['negativeAgeEnabled'] = true;
                }

                if (isset($_POST['negative-display']))
                {
                    $options['negativeAgeDisplay'] = $_POST['negative-display'];
                }

                if (isset($_POST['negative-display-custom']))
                {
                    $options['negativeAgeCustomDisplay'] = $_POST['negative-display-custom'];
                }

                if (isset($_POST['negative-display-position']))
                {
                    $options['negativeAgePosition'] = intval($_POST['negative-display-position']);
                }

                update_option($this->adminOptionsTitle, $options);
                ?>
                <div class="updated">
                    <strong><?php _e("Settings Updated.", "HowOldAmI");?></strong>
                </div>
                <?php
            }
            ?>
            <div class=wrap>
                <div class="icon32" id="icon-options-general"><br/></div>
                <h2>How Old Am I Settings</h2>
                <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                    <h3>Enter your birth date:</h3>
                    <p>
                        <select name="birthDay">
                            <?php for ($i = 1; $i < 32; $i++) { ?>
                                <option value="<?php echo $i; ?>" <?php if($options['birthDay']==$i) { _e('selected="selected"', "HowOldAmI"); } ?>><?php if($i < 10) { echo '0'; } echo $i; ?></option>
                            <?php } ?>
                        </select>
                        <select name="birthMonth">
                            <?php for ($i = 1; $i < 13; $i++) { ?>
                                <option value="<?php echo $i; ?>" <?php if($options['birthMonth']==$i) { _e('selected="selected"', "HowOldAmI"); } ?>><?php echo date('F', strtotime("2012-$i-01")); ?></option>
                            <?php } ?>
                        </select>
                        <input name="birthYear" type="text" value="<?php echo $options['birthYear']; ?>" class="small-text" placeholder="year" />
                    </p>
                    <h3>Select format:</h3>
                    <p>
                        <input type="radio" id="format-year" name="format" value="year" <?php if ($options['format'] == "year") { _e('checked="checked"', "HowOldAmI"); }?> />
                        <label for="format-year">Years (number) <span class="description">(e.g. "26")</span></label>
                    </p>
                    <p>
                        <input type="radio" id="format-year-words" name="format" value="year-words" <?php if ($options['format'] == "year-words") { _e('checked="checked"', "HowOldAmI"); }?> />
                        <label for="format-year-words">Years (in full) <span class="description">(e.g. "twenty-six")</span></label>
                    </p>
                    <p>
                        <input type="radio" id="format-year-month" name="format" value="year-month" <?php if ($options['format'] == "year-month") { _e('checked="checked"', "HowOldAmI"); }?> />
                        <label for="format-year-month">Years &amp; Months (number) <span class="description">(e.g. "26 years and 11 months")</span></label>
                    </p>
                    <p>
                        <input type="radio" id="format-year-month-words" name="format" value="year-month-words" <?php if ($options['format'] == "year-month-words") { _e('checked="checked"', "HowOldAmI"); }?> />
                        <label for="format-year-month-words">Years &amp; Months (in full) <span class="description">(e.g. "twenty-six years and eleven months")</span></label>
                    </p>
                    <p>
                        <input type="radio" id="format-custom" name="format" value="custom" <?php if ($options['format'] == "custom") { _e('checked="checked"', "HowOldAmI"); }?> />
                        <label for="format-custom">Custom</label> <input name="custom-format" type="text" value="<?php echo htmlentities(stripslashes($options['custom-format'])); ?>" class="regular-text" placeholder="" />
                        <p>You can use the following tags in custom mode:</p>
                        <ul class="ul-disc">
                            <li><code>%years%</code> &#8212; age in years (number) <span class="description">(e.g. "25")</span></li>
                            <li><code>%months%</code> &#8212; remaining months (number), to be used with years <span class="description">(e.g. "3")</span></li>
                            <li><code>%days%</code> &#8212; remaining days (number), to be used with months &amp; years <span class="description">(e.g. "23")</span></li> 
                            <li><code>%total-days%</code> &#8212; total age in days (number) <span class="description">(e.g. "9360")</span></li>
                            <li><code>%w-years%</code> &#8212; age in years (in full) <span class="description">(e.g. "twenty-five")</span></li>
                            <li><code>%w-months%</code> &#8212; remaining months (in full), to be used with years <span class="description">(e.g. "three")</span></li>
                            <li><code>%w-days%</code> &#8212; remaining days (in full), to be used with months &amp; years <span class="description">(e.g. "twenty-three")</span></li> 
                            <li><code>%w-total-days%</code> &#8212; total age in days (in full) <span class="description">(e.g. "nine thousand, three hundred and sixty")</span></li>
                        </ul>
                    </p>
                    <h3>Select how the age is calculated:</h3>
                    <p>Please note that this option will work only if the "Years" format is selected.</p>
                    <p>
                        <input type="radio" id="calculation-absolute" name="calculationType" value="absolute" <?php if ($options['calculationType'] == "absolute") { _e('checked="checked"', "HowOldAmI"); }?> />
                        <label for="calculation-absolute">Absolute <span class="description">(e.g. if you are 25 years and 11 months old, it will display "25")</span></label>
                    </p>
                    <p>
                        <input type="radio" id="calculation-relative" name="calculationType" value="relative" <?php if ($options['calculationType'] == "relative") { _e('checked="checked"', "HowOldAmI"); }?> />
                        <label for="calculation-relative">Relative <span class="description">(e.g. if you are 25 years and 6 months old or over, it will display "26")</span></label>
                    </p>
                    <h3>Select how to deal with negative ages:</h3>
                    <p>
                        <input type="checkbox" id="negative-display-status" name="negative-display-status" <?php if($options['negativeAgeEnabled']) { _e('checked="checked"', "HowOldAmI"); } ?>/>
                        <label for="negative-display-status">Show negative ages <span class="description">(if disabled, all negative ages will be displayed without a symbol or word indicating that they are negative)</span></label>
                    </p>
                    <p>Select which symbol or word will be displayed in case of a negative age:</p>
                    <p>
                        <input type="radio" id="negative-display-symbol" name="negative-display" value="symbol" <?php if ($options['negativeAgeDisplay'] == "symbol") { _e('checked="checked"', "HowOldAmI"); }?> />
                        <label for="negative-display-symbol">Minus symbol <span class="description">(e.g. "−26")</span></label>
                    </p>
                    <p>
                        <input type="radio" id="negative-display-word" name="negative-display" value="word" <?php if ($options['negativeAgeDisplay'] == "word") { _e('checked="checked"', "HowOldAmI"); }?> />
                        <label for="negative-display-word">Minus word <span class="description">(e.g. "minus 26")</span></label>
                    </p>
                    <p>
                        <input type="radio" id="negative-display-custom" name="negative-display" value="custom" <?php if ($options['negativeAgeDisplay'] == "custom") { _e('checked="checked"', "HowOldAmI"); }?> />
                        <label for="negative-display-custom">Custom</label> <input name="negative-display-custom" type="text" value="<?php echo htmlentities(stripslashes($options['negativeAgeCustomDisplay'])); ?>" class="regular-text" placeholder="" />
                    </p>
                    <p>Select the position of the negative symbol or word:</p>
                    <select name="negative-display-position">
                        <option value="0" <?php if ($options['negativeAgePosition'] == "0") { _e('selected="selected"', "HowOldAmI"); }?>>Before the age</option>
                        <option value="1" <?php if ($options['negativeAgePosition'] == "1") { _e('selected="selected"', "HowOldAmI"); }?>>After the age</option>
                    </select>
                    <p class="submit">
                        <input type="submit" name="updateHowOldAmI" value="<?php _e('Update Settings', 'HowOldAmI') ?>" class="button-primary" />
                    </p>
                </form>
                <h2>Usage</h2>
                <p>Select your date of birth and enter the shortcode <strong>[how-old-am-i]</strong> in any post or page.</p>
                <p>The following attributes are available to be used in the shortcode (the attributes can be combined):</p>
                <ul class="ul-disc">
                     <li><code>on</code> &#8212; takes as an argument either a date (format: YYYY-MM-DD) and overrides the current date, or the word "post" and uses the date &amp; time of the post to override the current date</li>
                     <li><code>bday</code> &#8212; takes as an argument either a date (format: YYYY-MM-DD) and overrides the birth date set on the plugin's settings, or the word "post" and uses the date &amp; time of the post to override the birth date</li>
                </ul>
                <p>Some examples using the attributes:</p>
                <ul class="ul-disc">
                     <li><code>[how-old-am-i on="2013-03-01"]</code> &#8212; displays the age as it was on the 1st of March, 2013</li>
                     <li><code>[how-old-am-i on="post"]</code> &#8212; displays the age as it was on the date the post was published on</li>
                     <li><code>[how-old-am-i bday="1980-02-22"]</code> &#8212; displays the age using as a birth date the 22nd of February, 1980</li>
                     <li><code>[how-old-am-i bday="post"]</code> &#8212; displays the age using as a birth date the date that the post was published on</li>
                     <li><code>[how-old-am-i on="2013-03-01" bday="1980-02-22"]</code> &#8212; displays the age of a person born on the 22nd of February, 1980, as it was on the 1st of March, 2013, ignoring both the birth date set in the plugin's setting, and the current date</li>
                     <li><code>[how-old-am-i on="post" bday="1980-02-22"]</code> &#8212; the same example as before, using the publish date of the post as the current date</li>
                     <li><code>[how-old-am-i on="2013-03-01" bday="post"]</code> &#8212; the same example as before, using the publish date of the post as the birth date</li>
                </ul>
                <h2>Support &amp; feedback</h2>
                <p>For questions, issues, or feature requests, you can <a href="http://burnmind.com/contact">contact me</a>, or post them either in the <a href="http://wordpress.org/tags/how-old-am-i">WordPress Forum</a> (make sure to add the tag "how-old-am-i"), or in <a href="http://burnmind.com/freebies/how-old-am-i">this</a> blog post.</p>
                <h2>How to contribute</h2>
                <ul>
                    <li>&raquo; Source code on <a href="https://github.com/stathisg/how-old-am-i">GitHub</a>.</li>
                    <li>&raquo; Blog about or link to the plugin so others can learn about it.</li>
                    <li>&raquo; Report issues, request features, provide feedback, etc.</li>
                    <li>&raquo; <a href="http://wordpress.org/extend/plugins/how-old-am-i/">Rate and/or review</a> the plugin</li>
                    <li>&raquo; <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JT4GXFTBH99LS">Make a donation</a></li>
                </ul>
                <h2>Other links</h2>
                <ul>
                    <li>&raquo; <a href="http://burnmind.com">my blog</a></li>
                    <li>&raquo; <a href="http://twitter.com/stathisg">@stathisg</a></li>
                    <li>&raquo; <a href="http://wordpress.org/extend/plugins/hello-in-all-languages/">Hello in all languages</a> WordPress plugin</li>
                </ul>
                <h2>Credits</h2>
                <p>This plugin is using some code by Karl Rixon &amp; Emil H. You can find more details in the plugin's source code.</p>
            </div>
            <?php
        }
    }
}

if (class_exists("howOldAmI"))
{
    $howOldAmI = new HowOldAmI();
}

if (!function_exists("howOldAmIAdmin"))
{
    function howOldAmIAdmin()
    {
        global $howOldAmI;
        if (!isset($howOldAmI))
        {
            return;
        }
        if (function_exists('add_options_page'))
        {
            add_options_page('How Old Am I Settings', 'How Old Am I', 'manage_options', basename(__FILE__), array(&$howOldAmI, 'printAdminPage'));
        }
    }
}

if (isset($howOldAmI))
{
    add_shortcode('how-old-am-i', array( &$howOldAmI, 'displayAge'));
    add_action('admin_menu', 'howOldAmIAdmin');
}

function howOldAmISettingsLink($links)
{ 
  $settings_link = '<a href="options-general.php?page=how-old-am-i.php">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'howOldAmISettingsLink' );
?>