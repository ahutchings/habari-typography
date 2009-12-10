<?php

class Typography extends Plugin
{
	private $typo;

    public function info()
    {
        return array(
            'url' => 'http://andrewhutchings.com/projects',
            'name' => 'Typography',
            'description' => 'Improves content typography with hyphenation, space control, intelligent character replacement, and CSS hooks for styling.',
            'license' => 'Apache License 2.0',
            'author' => 'Andrew Hutchings',
            'authorurl' => 'http://andrewhutchings.com/',
            'version' => '0.0.4'
        );
    }

    public function help()
    {
    	return <<<HELP
    	<p>This plugin emcompasses some typography-related features provided by
    	other plugins.</p>
    	<p>To avoid any potential issues:
	    	<ul>
	    		<li>If using the Habari Markdown plugin, disable SmartyPants</li>
	    		<li>Disable Typogrify</li>
	    	</ul>
    	</p>
HELP;
    }

    public function filter_plugin_config($actions, $plugin_id)
	{
		if ($plugin_id == $this->plugin_id()) {
			$actions[] = _t('Configure');
		}

		return $actions;
	}

	public function action_plugin_ui($plugin_id, $action)
	{
		if ($plugin_id == $this->plugin_id()) {
			if ($action == _t('Configure')) {

				$class_name = strtolower(get_class($this));

				$form = new FormUI($class_name);

				$ga = $form->append('fieldset', 'general_attributes', 'General Attributes');
				$ga->append('textmulti', 'tags_to_ignore', 'typography__tags_to_ignore', _t('Tags where typography of children will be untouched.'));
				$ga->append('textmulti', 'classes_to_ignore', 'typography__classes_to_ignore', _t('Classes where typography of children will be untouched.'));
				$ga->append('textmulti', 'ids_to_ignore', 'typography__ids_to_ignore', _t('IDs where typography of children will be untouched.'));

				$sc = $form->append('fieldset', 'smart_characters', 'Smart Characters');
				$sc->append('checkbox', 'smart_quotes', 'typography__smart_quotes', _t('Curl Quotemarks'));
				$sc->append('select', 'smart_quotes_language', 'typography__smart_quotes_language', _t('Language preference for curling quotemarks.'));
				$sc->smart_quotes_language->options = array(
					'en' => 'English style quotes',
					'de' => 'German style quotes',
					'fr' => 'French guillemets',
					'fr-reverse' => 'Reverse French guillemets'
				);
				$sc->append('checkbox', 'smart_dashes', 'typography__smart_dashes', _t('Replace "a--a" with <a href="http://en.wikipedia.org/wiki/Dash#En_dash">en dash</a> " -- " and "---" with <a href="http://en.wikipedia.org/wiki/Dash#Em_dash">em dash</a>.'));
				$sc->append('checkbox', 'smart_ellipses', 'typography__smart_ellipses', _t('Replace "..." with "…".'));
				$sc->append('checkbox', 'smart_marks', 'typography__smart_marks', _t('Replace (r) (c) (tm) (sm) (p) (R) (C) (TM) (SM) (P) with ® © ™ ℠ ℗.'));
				$sc->append('checkbox', 'smart_ordinal_suffix', 'typography__smart_ordinal_suffix', _t('Wrap numbers in <code>&lt;span class="numbers"&gt;</code>.'));
				$sc->append('checkbox', 'smart_math', 'typography__smart_math', _t('Use smart characters and spacing in math equations.'));
				$sc->append('checkbox', 'smart_fractions', 'typography__smart_fractions', _t('Replace 1/4  with <sup>1</sup>&#8260;<sub>4</sub>.'));
				$sc->append('checkbox', 'smart_exponents', 'typography__smart_exponents', _t('Replace 4^2  with 4<sup>2</sup>.'));

				$ss = $form->append('fieldset', 'smart_spacing', 'Smart Spacing');
				$ss->append('checkbox', 'single_character_word_spacing', 'typography__single_character_word_spacing', _t('Force single character words to next line with insertion of &amp;nbsp;.'));
				$ss->append('checkbox', 'fraction_spacing', 'typography__fraction_spacing', _t('Keep fractions together with insertion of &amp;nbsp;.'));
				$ss->append('checkbox', 'unit_spacing', 'typography__unit_spacing', _t('Keep units and values are together with insertion of &amp;nbsp;.'));
				$ss->append('textmulti', 'units', 'typography__units', _t('Units to keep with their values.'));
				$ss->append('checkbox', 'dash_spacing', 'typography__dash_spacing', _t('Wrap em and en dashes in thin spaces.'));
				$ss->append('checkbox', 'dewidow', 'typography__dewidow', _t('Try to prevent <a href="http://en.wikipedia.org/wiki/Widows_and_orphans">widows</a> by adding <code>&amp;nbsp;</code> between the last two words in blocks of text.'));
				$ss->append('text', 'max_dewidow_length', 'typography__max_dewidow_length', _t('Maximum length of a widow that will be protected.'));
				$ss->append('text', 'max_dewidow_pull', 'typography__max_dewidow_pull', _t('Maximum length of pulled text to keep widows company.'));
				$ss->append('checkbox', 'wrap_hard_hyphens', 'typography__wrap_hard_hyphens', _t('Enable wrapping at hard hyphens internal to a word with the insertion of a zero-width-space.'));
				$ss->append('checkbox', 'url_wrap', 'typography__url_wrap', _t('Enable wrapping of URLs.'));
				$ss->append('checkbox', 'email_wrap', 'typography__email_wrap', _t('Enable wrapping of email addresses.'));
				$ss->append('text', 'min_after_url_wrap', 'typography__min_after_url_wrap', _t('Minimum character requirement after a url wrapping point.'));

				$cs = $form->append('fieldset', 'character_styling', 'Character Styling');
				$cs->append('checkbox', 'style_ampersands', 'typography__style_ampersands', _t('Wrap ampersands in <code>&lt;span class="amp"&gt;</code>.'));
				$cs->append('checkbox', 'style_caps', 'typography__style_caps', _t('Wrap consecutive capital letters (acronyms, etc.) in <code>&lt;span class=&quot;caps&quot;&gt;</code>.'));
				$cs->append('checkbox', 'style_initial_quotes', 'typography__style_initial_quotes', _t('Wrap initial double quotes in <code>&lt;span class=&quot;dquo&quot;&gt;</code>, and initial single quotes in <code>&lt;span class=&quot;quo&quot;&gt;</code>.'));
				$cs->append('checkbox', 'style_numbers', 'typography__style_numbers', _t('Wrap numbers in <code>&lt;span class="numbers"&gt;</code>.'));
				$cs->append('textmulti', 'initial_quote_tags', 'typography__initial_quote_tags', _t('Tags where initial quotes and guillemets should be styled.'));

				$hp = $form->append('fieldset', 'hyphenation', 'Hyphenation');
				$hp->append('checkbox', 'hyphenation', 'typography__hyphenation', _t('Enable hyphenation of text.'));
				$hp->append('select', 'hyphenation_language', 'typography__hyphenation_language', _t('Hyphenation language for text.'));
				$hp->hyphenation_language->options = $this->typo->get_languages();
				$hp->append('text', 'min_length_hyphenation', 'typography__min_length_hyphenation', _t('Minimum length of a word that may be hyphenated.'));
				$hp->append('text', 'min_before_hyphenation', 'typography__min_before_hyphenation', _t('Minimum character requirement before a hyphenation point.'));
				$hp->append('text', 'min_after_hyphenation', 'typography__min_after_hyphenation', _t('Minimum character requirement after a hyphenation point.'));
				$hp->append('checkbox', 'hyphenate_headings', 'typography__hyphenate_headings', _t('Enable hyphenation of title/heading text.'));
				$hp->append('checkbox', 'hyphenate_all_caps', 'typography__hyphenate_all_caps', _t('Hyphenate strings with all capital characters.'));
				$hp->append('checkbox', 'hyphenate_title_case', 'typography__hyphenate_title_case', _t('Hyphenate strings starting with a capital character.'));
				$hp->append('textmulti', 'hyphenation_exceptions', 'typography__hyphenation_exceptions', _t('Custom word hyphenations (words with all hyphenation points marked with a hard hyphen).'));

				$ao = $form->append('fieldset', 'additional_options', 'Additional Options');
				$ao->append('checkbox', 'title_case', 'typography__title_case', _t( 'Attempt to properly capitalize post titles based on <a href="http://daringfireball.net/2008/05/title_case">rules</a> by John Gruber.'));

				$form->append('submit', 'save', _t('Save'));

				$form->on_success(array($this, 'updated_config'));
				$form->out();
			}
		}
	}

	public function updated_config($form)
	{
		$form->save();
	}

	public function get_default_options()
	{
		$defaults = array(
			// general attributes
			'tags_to_ignore' => array("code", "head", "kbd", "object", "option", "pre", "samp", "script", "select", "style", "textarea", "title", "var", "math"),
			'classes_to_ignore' => array("vcard", "noTypo"),
			'ids_to_ignore' => array(),

			// smart characters
			'smart_quotes' => true,
			'smart_quotes_language' => 'en',
			'smart_dashes' => true,
			'smart_ellipses' => true,
			'smart_marks' => true,
			'smart_ordinal_suffix' => true,
			'smart_math' => true,
			'smart_fractions' => true,
			'smart_exponents' => true,

			// smart spacing
			'single_character_word_spacing' => true,
			'fraction_spacing' => true,
			'unit_spacing' => true,
			'units' => array(),
			'dash_spacing' => true,
			'dewidow' => true,
			'max_dewidow_length' => 5,
			'max_dewidow_pull' => 5,
			'wrap_hard_hyphens' => true,
			'url_wrap' => true,
			'email_wrap' => true,
			'min_after_url_wrap' => 5,

			// character styling
			'style_ampersands' => true,
			'style_caps' => true,
			'style_initial_quotes' => true,
			'style_numbers' => true,
			'initial_quote_tags' => array("p", "h1", "h2", "h3", "h4", "h5", "h6", "blockquote", "li", "dd", "dt"),

			// hyphenation
			'hyphenation' => true,
			'hyphenation_language' => "en-US",
			'min_length_hyphenation' => 5,
			'min_before_hyphenation' => 3,
			'min_after_hyphenation' => 2,
			'hyphenate_headings' => true,
			'hyphenate_all_caps' => true,
			'hyphenate_title_case' => true,
			'hyphenation_exceptions' => array(),

			// additional options
			'title_case' => true
		);

		foreach ($defaults as $k => $v) {
			$plugin_defaults['typography__'.$k] = $v;
		}

		return $plugin_defaults;
	}

	public function action_plugin_activation($file)
	{
		if (Plugins::id_from_file($file) == Plugins::id_from_file(__FILE__)) {

			$options = $this->get_default_options();

			foreach ($options as $option => $value) {
				if (Options::get($option) == null) {
					Options::set($option, $value);
				}
			}
		}
	}

	public function filter($text)
	{
		$text = $this->typo->process($text);

		return $text;
	}

	public function filter_post_title_out($title)
	{
		if (Options::get('typography__title_case')) {
			$title = Typogrify::title_case($title);
		}

		// @todo work out which phpTypography filters won't break titles

		return $title;
	}

	public function filter_post_content_out($content)
	{
		return $this->filter($content);
	}

	public function filter_post_excerpt_out($excerpt)
	{
		return $this->filter($excerpt);
	}

	public function filter_comment_content_out($comment)
	{
		return $this->filter($comment);
	}

	public function filter_comment_name_out($name)
	{
		return $this->filter($name);
	}

	public function filter_post_tags_out($tags)
	{
		return $this->filter($tags);
	}

	/**
	 * Set all our filters to run after everything else did
	 */
	public function set_priorities()
	{
		return array(
			'filter_post_title_out' => 10,
			'filter_post_content_out' => 10,
			'filter_post_excerpt_out' => 10,
			'filter_comment_content_out' => 10,
			'filter_comment_name_out' => 10,
			'filter_post_tags_out' => 10,
		);
	}

	public function action_init()
	{
		include 'lib/php-typography/php-typography.php';
		include 'lib/typogrify.php';

		$opts = Options::get_group('typography');

		$typo = new phpTypography();

		// general attributes
		$typo->set_tags_to_ignore($opts['tags_to_ignore']);
		$typo->set_classes_to_ignore($opts['classes_to_ignore']);
		$typo->set_ids_to_ignore($opts['ids_to_ignore']);

		// smart characters
		$typo->set_smart_quotes($opts['smart_quotes']);
		$typo->set_smart_quotes_language($opts['smart_quotes_language']);
		$typo->set_smart_dashes($opts['smart_dashes']);
		$typo->set_smart_ellipses($opts['smart_ellipses']);
		$typo->set_smart_marks($opts['smart_marks']);
		$typo->set_smart_ordinal_suffix($opts['smart_ordinal_suffix']);
		$typo->set_smart_math($opts['smart_math']);
		$typo->set_smart_fractions($opts['smart_fractions']);
		$typo->set_smart_exponents($opts['smart_exponents']);

		// smart spacing
		$typo->set_single_character_word_spacing($opts['single_character_word_spacing']);
		$typo->set_fraction_spacing($opts['fraction_spacing']);
		$typo->set_unit_spacing($opts['unit_spacing']);
		$typo->set_units($opts['units']);
		$typo->set_dash_spacing($opts['dash_spacing']);
		$typo->set_dewidow($opts['dewidow']);
		$typo->set_max_dewidow_length($opts['max_dewidow_length']);
		$typo->set_max_dewidow_pull($opts['max_dewidow_pull']);
		$typo->set_wrap_hard_hyphens($opts['wrap_hard_hyphens']);
		$typo->set_url_wrap($opts['url_wrap']);
		$typo->set_email_wrap($opts['email_wrap']);
		$typo->set_min_after_url_wrap($opts['min_after_url_wrap']);

		// character styling
		$typo->set_style_ampersands($opts['style_ampersands']);
		$typo->set_style_caps($opts['style_caps']);
		$typo->set_style_initial_quotes($opts['style_initial_quotes']);
		$typo->set_style_numbers($opts['style_numbers']);
		$typo->set_initial_quote_tags($opts['initial_quote_tags']);

		// hyphenation
		$typo->set_hyphenation($opts['hyphenation']);
		$typo->set_hyphenation_language($opts['hyphenation_language']);
		$typo->set_min_length_hyphenation($opts['min_length_hyphenation']);
		$typo->set_min_before_hyphenation($opts['min_before_hyphenation']);
		$typo->set_min_after_hyphenation($opts['min_after_hyphenation']);
		$typo->set_hyphenate_headings($opts['hyphenate_headings']);
		$typo->set_hyphenate_all_caps($opts['hyphenate_all_caps']);
		$typo->set_hyphenate_title_case($opts['hyphenate_title_case']);
		$typo->set_hyphenation_exceptions($opts['hyphenation_exceptions']);

		$this->typo = $typo;
	}
}

?>
