<?php
require_once 'osc_functions.php';
class Export_Project extends GP_Plugin {
    function __construct() {
        parent::__construct();

        $this->add_action( 'subprojects_menu_item' );

        GP::$router->add( '/fullexport', array('Export_Project', 'fullexport_view'), 'get');
        GP::$router->add( '/fullexport', array('Export_Project', 'fullexport_post'), 'post');
    }

    function before_request() { }
    function register_shutdown_function() { }
    function after_request() { }

    function subprojects_menu_item($project) {
        if(stripos($project->path, 'core')===false && stripos($project->path, 'modern')===false && stripos($project->path, 'flash-messages')===false && stripos($project->path, 'email-templates')===false) {
            echo '<span id="prj_'.$project->id.'" style="font-size:0.7em;">';
            echo gp_link( gp_url(
                        '/fullexport'
                        ,array(
                            'project_path' => $project->path
                        )
                    )
                    ,__('Export')
                    );
            echo '</span>';
        }
    }


    static function fullexport_view() {
        global $gpdb;
		$project_path = gp_get( 'project_path', null );
        $gp_prj = new GP_Project();
        $project = $gp_prj->by_path($project_path);
        $tree = self::projectTree($project->id);

        $locales = array();
        foreach($tree as $id) {
            $translation_sets = GP::$translation_set->by_project_id( $id );
            foreach( $translation_sets as $set ) {
                $locale = $set->locale;
                $locales[$locale]['locale'] = $locale;
                $locales[$locale]['name'] = $set->name_with_locale();
                $locales[$locale]['current'] = @$locales[$locale]['current']+$set->current_count();
                $locales[$locale]['all'] = @$locales[$locale]['all']+$set->all_count();
                $locales[$locale]['percent'] = ((int)(1000*$locales[$locale]['current']/$locales[$locale]['all']))/10;
            }
        }
		gp_tmpl_load( 'project-export', get_defined_vars() );
    }

    static function fullexport_post() {
        global $gpdb;
		$project_id = gp_post( 'project_id', null);
        $locale = gp_post( 'export_locale', null);

        $locale_fixed = self::fixLocale($locale);

        $prjs = self::subProjectsOf($project_id);

        @mkdir(dirname(__FILE__)."/tmp/".$locale_fixed, 0777);
        $path = dirname(__FILE__)."/tmp/".$locale_fixed."/";

        foreach($prjs as $prj) {
            $project = $gpdb->get_row("SELECT * FROM `$gpdb->projects` WHERE id = ".$prj);
            if($project->slug=='core') {
                $filename = $path."core.po";
                $lines = self::POGetLines('PO', $project, $locale, $filename);
                $filename = $path."core.mo";
                $lines = self::POGetLines('MO', $project, $locale, $filename);
            }
            if($project->slug=='modern') {
                $filename = $path."theme.po";
                $lines = self::POGetLines('PO', $project, $locale, $filename);
                $filename = $path."theme.mo";
                $lines = self::POGetLines('MO', $project, $locale, $filename);
            }
            if($project->slug=='flash-messages') {
                $filename = $path."messages.po";
                $lines = self::POGetLines('PO', $project, $locale, $filename);
                $filename = $path."messages.mo";
                $lines = self::POGetLines('MO', $project, $locale, $filename);
            }
            if($project->slug=='email-templates') {
                $lines = self::POGetLines($project, $locale, null);
                $template = file_get_contents(dirname(__FILE__)."/tmp/template_mail.sql");
                foreach($lines as $line) {
                    $template = str_replace($line->singular, @$line->translations[0], $template);
                }

                $template = str_replace("en_US", $locale_fixed, $template);

                $filename = $path."mail.sql";
                $fh = fopen($filename, 'w');
                $res = fwrite($fh, $template);
                fclose($fh);
            }
        }

        $project = $gpdb->get_row("SELECT * FROM `$gpdb->projects` WHERE id = ".$project_id);
        $tree = self::projectTree($project->id);
        //( project_id = ".implode(" || project_id = ", $tree)." ) AND
        $tset = $gpdb->get_row("SELECT * FROM `$gpdb->translation_sets` WHERE  locale = '".$locale."'");

$template = '<?php'.PHP_EOL.
'/*'.PHP_EOL.
' *      Osclass – software for creating and publishing online classified'.PHP_EOL.
' *                           advertising platforms'.PHP_EOL.
' *'.PHP_EOL.
' *                        Copyright (C) 2012 OSCLASS'.PHP_EOL.
' *'.PHP_EOL.
' *       This program is free software: you can redistribute it and/or'.PHP_EOL.
' *     modify it under the terms of the GNU Affero General Public License'.PHP_EOL.
' *     as published by the Free Software Foundation, either version 3 of'.PHP_EOL.
' *            the License, or (at your option) any later version.'.PHP_EOL.
' *'.PHP_EOL.
' *     This program is distributed in the hope that it will be useful, but'.PHP_EOL.
' *         WITHOUT ANY WARRANTY; without even the implied warranty of'.PHP_EOL.
' *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'.PHP_EOL.
' *             GNU Affero General Public License for more details.'.PHP_EOL.
' *'.PHP_EOL.
' *      You should have received a copy of the GNU Affero General Public'.PHP_EOL.
' * License along with this program.  If not, see <http://www.gnu.org/licenses/>.'.PHP_EOL.
' */'.PHP_EOL.
''.PHP_EOL.
'function locale_'.$locale_fixed.'_info() {'.PHP_EOL.
'    return array('.PHP_EOL.
'         \'name\'            => \''.$tset->name.'\''.PHP_EOL.
'        ,\'short_name\'      => \''.preg_replace('| \((.+)\)|', '', $tset->name).'\''.PHP_EOL.
'        ,\'description\'     => \''.$tset->name.' translation\''.PHP_EOL.
'        ,\'version\'         => \''.$project->name.'\''.PHP_EOL.
'        ,\'author_name\'     => \'Osclass\''.PHP_EOL.
'        ,\'author_url\'      => \'http://osclass.org/\''.PHP_EOL.
'        ,\'currency_format\' => \'{NUMBER} {CURRENCY}\''.PHP_EOL.
'        ,\'date_format\'     => \'m/d/Y\''.PHP_EOL.
'        ,\'stop_words\'      => \'\''.PHP_EOL.
'    );'.PHP_EOL.
'}'.PHP_EOL.
''.PHP_EOL.
'?>';


        $filename = $path."index.php";
        $zip_path = dirname(__FILE__)."/tmp/lang_".$locale_fixed."_".$project->name.".zip";
        $fh = fopen($filename, 'w');
        $res = fwrite($fh, $template);
        fclose($fh);

        if(!defined('TMP_PATH')) {
            define('TMP_PATH', dirname(__FILE__)."/tmp/");
        }
        osc_zip_folder($path, $zip_path);
        osc_deleteDir($path);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($zip_path));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($zip_path));
        @ob_clean();
        flush();
        readfile($zip_path);

        // remove ourselves
        @unlink($zip_path);
        exit;

    }

    static function POGetLines($class, $project, $locale_slug, $filename = null) {

        $locale = GP_Locales::by_slug( $locale_slug );
		$translation_set = GP::$translation_set->by_project_id_slug_and_locale( $project->id, 'default', $locale_slug );

        $format = gp_array_get( GP::$formats, gp_get( 'format', 'po' ), null );
        $export_locale = apply_filters( 'export_locale', $locale->slug, $locale );
		$entries = GP::$translation->for_export( $project, $translation_set, gp_get( 'filters' ) );

        if($filename==null) {
            return $entries;
        }

        $po = new $class;
		$po->set_header( 'PO-Revision-Date', gmdate( 'Y-m-d H:i:s+0000' ) );
		$po->set_header( 'MIME-Version', '1.0' );
		$po->set_header( 'Content-Type', 'text/plain; charset=UTF-8' );
		$po->set_header( 'Content-Transfer-Encoding', '8bit' );
		$po->set_header( 'Plural-Forms', "nplurals=$locale->nplurals; plural=$locale->plural_expression;" );
		$po->set_header( 'X-Generator', 'GlotPress/' . gp_get_option('version') );

		// force export only current translations
		$filters['status'] = 'current';

		foreach( $entries as $entry ) {
			$po->add_entry( $entry );
		}
		$po->set_header( 'Project-Id-Version', $project->name );

		$po->comments_before_headers .= "Translation of {$project->name} in {$locale->english_name}\n";
		$po->comments_before_headers .= "This file is distributed under the same license as the {$project->name} package.\n";

        return $po->export_to_file($filename);

    }

    static function projectTree($project_id) {
        $gpj = new GP_Project();
        return self::subProjectsOf($project_id);
    }

    static function subProjectsOf($id) {
        global $gpdb;
        $prjs = $gpdb->get_results( "SELECT id FROM `$gpdb->projects` WHERE parent_project_id = ".$id );
        $subs = array();
        foreach($prjs as $prj) {
            $subs = array_merge($subs, self::subProjectsOf($prj->id));
        }
        if(empty($subs)) {
            return array($id);
        } else {
            return array_merge(array($id), $subs);
        }
    }

    static function fixLocale($locale) {
        $tmp = explode("-", $locale);
        if(count($tmp)==1) {
            return strtolower($tmp[0])."_".strtoupper($tmp[0]);
        }
        return strtolower($tmp[0])."_".strtoupper($tmp[1]);
    }

}

GP::$plugins->export_project = new Export_Project;

?>