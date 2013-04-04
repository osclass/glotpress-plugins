<?php

class Getting_Started extends GP_Plugin
{
    function __construct()
    {
        parent::__construct();

        $this->add_action( 'init' );
        $this->add_action( 'after_notices' );

        GP::$router->add( '/getting-started', 'getting_started_page' );
    }

    function init()
    {
        if( !GP::$user->logged_in() ) {
            gp_notice_set( sprintf(__( 'New to <em>translate.osclass.org</em>? Have a look at the <a href="%s">Getting Started guide.</a>', 'gettingstarted' ), 'http://translate.osclass.org/getting-
started'), 'gettingstarted' );
        }
    }

    function after_notices()
    {
        if ( gp_notice( 'gettingstarted' ) ) { ?>
            <div class="notice">
                <?php echo gp_notice( 'gettingstarted' ); ?>
            </div>
        <?php }
    }
}

function getting_started_page() {
    gp_title( __( 'Getting Started', 'gettingstarted' ) );
    gp_tmpl_header();
    echo '<h2>' . __( 'Getting started guide', 'gettingstarted' ) . '</h2>';
    echo '<p>';
    _e('Below you can find a small guide on how to use a GlotPress for Osclass translations.', 'gettingstarted');
    echo '</p>';
    echo '<h3>' . __('Organization', 'gettingstarted') . '</h3>';
    echo '<p>';
    _e('GlotPress is organized in projects and subprojects. You can find three major projects:', 'gettingstarted');
    echo '<ul>';
    echo '<li>' . __('<strong>Osclass</strong>: Every version of Osclass has its own subprojects, starting from the 2.1.x till 3.1.x. We have also created a "version in development" in order to minimize the time gap between a launch of new version and its availability in each language. Every subproject with different version of Osclass is divided into four parts: modern theme, flash messages, core and email templates.', 'gettingstarted') . '</li>';
    echo '<li>' . __('<strong>Plugins</strong>: There is a subproject for each plugin and every plugin can have its subprojects for each version of Osclass. The plugins available for translations are only those listed on <a href="http://market.osclass.org/">market.osclass.org</a>', 'gettingstarted') . '</li>';
    echo '<li>' . __('<strong>Themes</strong>: There is a subproject for each theme and every theme can have its subprojects for each version of Osclass. The themes available for translations are only those listed on <a href="http://market.osclass.org/">market.osclass.org</a>.', 'gettingstarted') . '</li>';
    echo '</ul>';
    _e('Each project has its own strings available only in English. Although it is possible to export them in different formats, the correct ones are <em>.po</em> or <em>.mo</em>.', 'gettingstarted');
    echo '</p>';
    echo '<h3>' . __('Users', 'gettingstarted') . '</h3>';
    echo '<p>';
    _e('There are two type of users: guests and translators. The guests can see all the projects, translations and export the translations in different formats. The translators can contribute to the requested language and make suggestions to translations in other languages. However, those suggestions must be approved by one of the translators of each language. <strong>If you do not have your username to enter GlotPress, you can request it <a href="http://osclass.org/contact">here</a></strong>. We will send you your login information as soon as possible.', 'gettingstarted');
    echo '</p>';
    echo '<h3>' . __('Getting started', 'gettingstarted') . '</h3>';
    echo '<p>';
    _e('If you want to contribute to translations, you have to log in to GlotPress. Choose a project (and the corresponding subproject) and a language you want to translate. You will see the listing with the strings and their corresponding translations. Use the links and filters above to find the strings you are looking for or the one that still need to be translated. The strings might have following status: current, waiting, untranslated, fuzzy and approved.', 'gettingstarted');
    echo '</p>';
    echo '<h3>' . __('Translating', 'gettingstarted') . '</h3>';
    echo '<p>';
    _e('You can start translating by double clicking on a string or by clicking on "Details". The string you want to translate will expand and a text box will appear. Introduce your translation there and provide all the information that might help to define the context for this string, such as a source code file with its location. Once the translation is added, the next string will open to be translated.', 'gettingstarted');
    echo '</p>';
    echo '<h3>' . __('Translation completed', 'gettingstarted') . '</h3>';
    echo '<p>';
    _e('If you think you have successfully finished your translations, send us an email at <a href="mailto:translations@osclass.org?Subject=Translation%20finished">translations@osclass.org</a> so we can publish it. We will notify you once it’s online :-)', 'gettingstarted');
    echo '</p>';

    gp_tmpl_footer();
}

GP::$plugins->getting_started = new Getting_Started;