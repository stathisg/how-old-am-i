<?php
/*
Plugin Name: How Old Am I
Plugin URI: http://burnmind.com/freebies/how-old-am-i
Version: 1.0.0
Author: Stathis Goudoulakis
Author URI: http://burnmind.com/
Description: Calculates and displays your age in several formats.

Copyright 2012 Stathis Goudoulakis (me@stathisg.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
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
                                  'birthYear' => 1970,
                                  'calculationType' => 'absolute',
                                  'format' => 'year',
                                  'custom-format' => '',
                                  'php' => 'latest');
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

        function displayAge()
        {
            $options = $this->getAdminOptions();

            $returnValue = '';

            if($options['php']==='latest')
            {
                $birthDate = new DateTime();
                $birthDate->setDate($options['birthYear'], $options['birthMonth'], $options['birthDay']);
                $now = new DateTime("now");
                $difference = $birthDate->diff($now);

                $yearsOld = $difference->y;
                $monthsOld = $difference->m;
                $daysOld = $difference->d;
                $totalDaysOld = $difference->days;
            }
            else
            {
                require_once('_date_diff.php');
                $birthDate = $options['birthYear'] . '-' . $options['birthMonth'] . '-' . $options['birthDay'];
                $difference = _date_diff(strtotime($birthDate), time());

                $yearsOld = $difference['y'];
                $monthsOld = $difference['m'];
                $daysOld = $difference['d'];
                $totalDaysOld = $difference['days'];
            }

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
                    $returnValue = convert_number_to_words($returnValue);
                }
            }
            else if($options['format']==='custom')
            {
                require_once('_convert_number_to_words.php');
                $returnValue = str_replace('%years%', $yearsOld, $options['custom-format']);
                $returnValue = str_replace('%months%', $monthsOld, $returnValue);
                $returnValue = str_replace('%days%', $daysOld, $returnValue);
                $returnValue = str_replace('%total-days%', $totalDaysOld, $returnValue);
                $returnValue = str_replace('%w-years%', convert_number_to_words($yearsOld), $returnValue);
                $returnValue = str_replace('%w-months%', convert_number_to_words($monthsOld), $returnValue);
                $returnValue = str_replace('%w-days%', convert_number_to_words($daysOld), $returnValue);
                $returnValue = str_replace('%w-total-days%', convert_number_to_words($totalDaysOld), $returnValue);
                $returnValue = stripslashes($returnValue);
            }
            else
            {
                if($options['format']==='year-month-words')
                {
                    require_once('_convert_number_to_words.php');
                    $yearsOld = convert_number_to_words($yearsOld);
                    $monthsOld = convert_number_to_words($monthsOld);
                }
                $returnValue = "$yearsOld years and $monthsOld months";
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

                if (isset($_POST['php']))
                {
                    $options['php'] = $_POST['php'];
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
                        <label for="format-custom">Custom</label> <input name="custom-format" type="text" value="<?php echo stripslashes($options['custom-format']); ?>" class="regular-text" placeholder="" />
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
                        <label for="calculation-relative">Relative <span class="description">(e.g. if you are 25 years and 6 months old or over, it will display "26";)</span></label>
                    </p>
                    <h3>PHP version:</h3>
                    <p>If you don't know which PHP version is installed on your server, try the plugin with the default option and if it doesn't work, switch to the other one.</p>
                    <select name="php">
                        <option value="latest" <?php if ($options['php'] == "latest") { _e('selected="selected"', "HowOldAmI"); }?>>5.3 or later</option>
                        <option value="old" <?php if ($options['php'] == "old") { _e('selected="selected"', "HowOldAmI"); }?>>Older than 5.3</option>
                    </select>
                    <p class="submit">
                        <input type="submit" name="updateHowOldAmI" value="<?php _e('Update Settings', 'HowOldAmI') ?>" class="button-primary" />
                    </p>
                </form>
                <h2>Usage</h2>
                <p>Select your date of birth and enter the shortcode <strong>[how-old-am-i]</strong> in any post or page.</p>
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
                    <li>&raquo; <a href="http://burnmind.com">burnmind.com</a></li>
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

function howOldAmISettingsLink($links) { 
  $settings_link = '<a href="options-general.php?page=how-old-am-i.php">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'howOldAmISettingsLink' );
?>