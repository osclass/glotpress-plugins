<?php

class Clone_Project extends GP_Plugin {
    function __construct() {
        parent::__construct();

        $this->add_action( 'project_actions_menu' );

        GP::$router->add( '/clone', array('Clone_Project', 'clone_view'), 'get');
        GP::$router->add( '/clone', array('Clone_Project', 'clone_post'), 'post');
    }

    function before_request() { }
    function register_shutdown_function() { }
    function after_request() { }

    function project_actions_menu($project) {
        echo '<li>';
        echo gp_link( gp_url(
                    '/clone'
                    ,array(
                        'name' => $project->name." COPY"
                        ,'slug' => $project->slug."_copy"
                        ,'description' => $project->description
                        ,'source_url_template' => $project->source_url_template
                        ,'parent_project_id' => $project->parent_project_id
                        ,'active' => $project->active
                        ,'original_project_id' => $project->id
                    )
                )
                ,__('Clone Project')
                );
        echo '</li>';
    }

    static function clone_view() {
        $project = new GP_Project();
		$project->name = gp_get( 'name', null );
		$project->slug = gp_get( 'slug', null );
		$project->description = gp_get( 'description', null );
		$project->source_url_template = gp_get( 'source_url_template', null );
		$project->parent_project_id = gp_get( 'parent_project_id', null );
		$project->active = gp_get( 'active', null );
		$project->original_project_id = gp_get( 'original_project_id', null );
		//if ( $this->cannot_and_redirect( 'write', 'project', $project->parent_project_id ) ) return;
		gp_tmpl_load( 'project-clone', get_defined_vars() );
    }

    static function clone_post() {
        global $gpdb;
        $original_project_id = gp_get( 'original_project_id' );
		$post = gp_post( 'project' );
		$parent_project_id = gp_array_get( $post, 'parent_project_id', null );
		//if ( $this->cannot_and_redirect( 'write', 'project', $parent_project_id ) ) return;
		$new_project = new GP_Project( $post );
		//if ( $this->invalid_and_redirect( $new_project ) ) return;
		$project = GP::$project->create_and_select( $new_project );
        self::clone_translations($original_project_id, $project->id);
        $children = $gpdb->get_results("SELECT id FROM `$gpdb->projects` WHERE parent_project_id = ".$original_project_id);
        foreach($children as $child) {
            self::clone_project($child->id, $project->id);
        }
		if ( !$project ) {
			$project = new GP_Project();
			$this->errors[] = __('Error in creating project!');
			gp_tmpl_load( 'project-new', get_defined_vars() );
		} else {
            $gprp = new GP_Route_Project;
			$gprp->notices[] = __('The project was created!');
			$gprp->redirect( gp_url_project( $project ) );
		}
    }

    static function clone_project($id, $parent_id = null) {
        global $gpdb;
        $project = $gpdb->get_row("SELECT * FROM `$gpdb->projects` WHERE id = ".$id);

        $prj = new GP_Project();
		$prj->name = $project->name;
		$prj->slug = $project->slug;
		$prj->description = $project->description;
		$prj->source_url_template = $project->source_url_template;
		$prj->parent_project_id = $parent_id==null?$project->parent_project_id:$parent_id;
		$prj->active = $project->active;
		$prj->original_project_id = $project->original_project_id;

        $new_prj = GP::$project->create_and_select($prj);
        self::clone_translations($id, $new_prj->id);
        $children = $gpdb->get_results("SELECT id FROM `$gpdb->projects` WHERE parent_project_id = ".$project->id);
        foreach($children as $child) {
            self::clone_project($child->id, $new_prj->id);
        }

    }

    static function clone_translations($from, $to) {

        global $gpdb;
        // ORIGINAL STRINGS
        $osets = GP::$original->by_project_id($from);
        $tmp = array();
        foreach($osets as $oset) {
            $gpdb->insert(
                $gpdb->originals
                ,array(
                    'project_id' => $to
                    ,'context' => $oset->context
                    ,'singular' => $oset->singular
                    ,'plural' => $oset->plural
                    ,'references' => $oset->references
                    ,'comment' => $oset->comments
                    ,'status' => $oset->status
                    ,'priority' => $oset->priority
                    ,'date_added' => date('Y-m-d H:i:s')
                )
            );
            $tmp[$oset->id] = $gpdb->insert_id;
        }

        // TRANSLATIONS SETS
        $tsets = GP::$translation_set->by_project_id($from);
        if(!empty($tsets)) {
            foreach($tsets as $tset) {
                $gpdb->insert($gpdb->translation_sets,array('name' => $tset->name,'slug' => $tset->slug, 'project_id' => $to,'locale' => $tset->locale));
                $tsetid = $gpdb->insert_id;
                $now = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
                $current_date = $now->format( DATE_MYSQL );

                $trans = $gpdb->get_results("SELECT * FROM `$gpdb->translations` WHERE translation_set_id = ".$from);
                foreach($trans as $tran) {
                    $gpdb->insert(
                        $gpdb->translations
                        ,array(
                            'original_id' => $tmp[$tran->original_id]
                            ,'translation_set_id' => $tsetid
                            ,'translation_0' => $tran->translation_0
                            ,'translation_1' => $tran->translation_1
                            ,'translation_2' => $tran->translation_2
                            ,'translation_3' => $tran->translation_3
                            ,'translation_4' => $tran->translation_4
                            ,'translation_5' => $tran->translation_5
                            ,'user_id' => $tran->user_id
                            ,'status' => $tran->status
                            ,'date_added' => $current_date
                            ,'date_modified' => $current_date
                            ,'warnings' => $tran->warnings
                        )
                    );
                }
            }
        }
    }


}

GP::$plugins->clone_project = new Clone_Project;

?>