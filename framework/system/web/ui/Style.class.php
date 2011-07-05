<?php
/**
 * Arquivo Style.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */

/**
 * Classe Style
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class Style
{
	/**
	 * Style Properties
	 */
	protected $id;
	
	/**
	 * The style properties
	 *
	 * @var array
	 */
	private $properties;
	
	/**
	 * The component wich this style belongs to
	 *
	 * @var Component
	 */
	private $owner;
//	/**
//	 * Tag Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $type;
//	protected $media;
//	/**#@-*/
//
//	/**
//	 * Background Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $background;
//	protected $background_attachment;
//	protected $background_color;
//	protected $background_image;
//	protected $background_position;
//	protected $background_repeat;
//	/**#@-*/
//
//	/**
//	 * Text Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $color;
//	protected $direction;
//	protected $letter_spacing;
//	protected $text_align;
//	protected $text_decoration;
//	protected $text_indent;
//	protected $text_shadow;
//	protected $text_transform;
//	protected $unicode_bidi;
//	protected $white_space;
//	protected $word_spacing;
//	/**#@-*/
//
//	/**
//	 * Font Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $font;
//	protected $font_family;
//	protected $font_size;
//	protected $font_size_adjust;
//	protected $font_stretch;
//	protected $font_style;
//	protected $font_variant;
//	protected $font_weight;
//	/**#@-*/
//
//	/**
//	 * Border Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $border_width;
//	protected $border_top_width;
//	protected $border_bottom_width;
//	protected $border_left_width;
//	protected $border_right_width;
//
//	protected $border_color;
//	protected $border_top_color;
//	protected $border_bottom_color;
//	protected $border_left_color;
//	protected $border_right_color;
//
//	protected $border_style;
//	protected $border_top_style;
//	protected $border_bottom_style;
//	protected $border_left_style;
//	protected $border_right_style;
//
//	protected $border;
//	protected $border_top;
//	protected $border_bottom;
//	protected $border_left;
//	protected $border_right;
//	/**#@-*/
//
//	/**
//	 * Margin Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $margin;
//	protected $margin_bottom;
//	protected $margin_left;
//	protected $margin_right;
//	protected $margin_top;
//	/**#@-*/
//
//	/**
//	 * Padding Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $padding;
//	protected $padding_bottom;
//	protected $padding_left;
//	protected $padding_right;
//	protected $padding_top;
//	/**#@-*/
//
//	/**
//	 * List and Marker Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $list_style;
//	protected $list_style_image;
//	protected $list_style_position;
//	protected $list_style_type;
//	protected $marker_offset;
//	/**#@-*/
//
//	/**
//	 * Dimension Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $height;
//	protected $line_height;
//	protected $max_height;
//	protected $max_width;
//	protected $min_height;
//	protected $min_width;
//	protected $width;
//	/**#@-*/
//
//	/**
//	 * Classification Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $clear;
//	protected $cursor;
//	protected $display;
//	protected $float;
//	protected $position;
//	protected $visibility;
//	/**#@-*/
//
//	/**
//	 * Positioning Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $bottom;
//	protected $clip;
//	protected $left;
//	protected $overflow;
//	protected $right;
//	protected $top;
//	protected $vertical_align;
//	protected $z_index;
//	/**#@-*/
//
//	/**
//	 * Generated Content Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $content;
//	protected $counter_increment;
//	protected $counter_reset;
//	protected $quotes;
//	/**#@-*/
//
//	/**
//	 * Generated Select Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	//protected $id;	[Propriedade Herdada]
//	protected $class;
//	/**#@-*/
//
//	/**
//	 * Outlines Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $outline;
//	protected $outline_color;
//	protected $outline_style;
//	protected $outline_width;
//	/**#@-*/
//
//	/**
//	 * Table Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $border_collapse;
//	protected $border_spacing;
//	protected $caption_side;
//	protected $empty_cells;
//	protected $table_layout;
//	/**#@-*/
//
//	/**
//	 * Print Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $orphans;
//	protected $marks;
//	protected $page;
//	protected $page_brake_after;
//	protected $page_brake_before;
//	protected $page_brake_inside;
//	protected $size;
//	protected $windows;
//	/**#@-*/
//
//	/**
//	 * Aural Properties
//	 *
//	 * @access protected
//	 * @var string
//	 */
//	protected $azimuth;
//	protected $cue;
//	protected $cue_before;
//	protected $cue_after;
//	protected $elevation;
//	protected $pause;
//	protected $pause_before;
//	protected $pause_after;
//	protected $pitch;
//	protected $pitch_range;
//	protected $play_during;
//	protected $richness;
//	protected $speak;
//	protected $speak_header;
//	protected $speak_numeral;
//	protected $speak_punctuation;
//	protected $speech_rate;
//	protected $strees;
//	protected $voice_family;
//	protected $volume;
//	/**#@-*/
	
	/**
	 * Constructor
	 *
	 * @param string $params The tag attributes
	 */
	public function __construct($params = null, Component $owner = null)
	{
		$this->properties = array();
		
		/*
		//print "params: " . $params;
		$props = get_class_vars("Style");

		//! Quental: para que colocar todos os atributos como null? Se é que é isso que está sendo feito.
		foreach($props as $key => $v)
		{
			$this->set($key,null);
		}
		*/
		if($params !== null)
		{
			// parsea os parâmetros passados para $properties
			$properties = PhpUtils::strParseStyle($params);

			foreach($properties as $p => $v)
			{
				$this->properties[$p] = $v;
			}
		}
		
		$this->owner = $owner;
	}

	/**
	 * Write the style.
	 */
	public function __toString()
	{
		$val = "";

		foreach($this->properties as $p => $v)
		{
			if($v != null)
				$val .= $p.":".$v."; ";
		}

		return $val;
	}

	/**
	 * Function set
	 *		Verifica se existe um método próprio para insercao da propriedade fazendo
	 *		a busca por set"NomeDaPropriedade"(). Se o método existir ele será chamado
	 *		para setar a propriedade.
	 *
	 * @param string $property
	 * @param string $value
	 *
	 * @return boolean true se a propriedade for setada e false se não for
	 */
	public function set($property, $value, $raiseChange = true)
	{
		if($property == 'id' || $property == 'owner' || $property == 'class')
		{
			$this->$property = $value;
			return;
		}
			
		$oldValue = isset($this->properties[$property]) ? $this->properties[$property] : null;
		
		if($value !== $oldValue)
		{
			$this->properties[$property] = $value;
			
			if($raiseChange && $this->owner != null)
				$this->owner->onPropertyChange($this->owner, array("propertyName" => "style", "oldValue" => $this->getPropertiesList()));
				
			return true;
		}

		return false;
	}

	/**
	 * Function get
	 *
	 * @param string $property
	 * @return string
	 */
	public function get($property)
	{
		if($property == 'id' || $property == 'owner' || $property == 'class')
			return $this->$property;
			
		return isset($this->properties[$property]) ? $this->properties[$property] : null;
	}
}