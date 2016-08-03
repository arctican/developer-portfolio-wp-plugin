<?php
/* rendering.php
All the functions for displaying the final output to the front end
-----------------------------------------------------------------------

Developer Portfolio is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Developer Portfolio is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Developer Portfolio. If not, see http://www.gnu.org/licenses/gpl.html
*/



// Add the CSS style to the site
function atc_dp_register_portfolio_styles()
{
    wp_register_style( 'portfolio-style', plugins_url( '../style.css', __FILE__ ));
    wp_enqueue_style( 'portfolio-style' );
}
add_action( 'wp_enqueue_scripts', 'atc_dp_register_portfolio_styles' );



/** Hook into content filter to show the tags */
function atc_dp_the_content_filter($content)
{

	if (get_post_type() == 'atc_dp_projects' && is_single())
  		return atc_dp_render_portfolio_tags(false) . "<hr>" . $content;
	else
		return $content;
}
add_filter( 'the_content', 'atc_dp_the_content_filter' );



/** Renders the portfolio tags */
function atc_dp_render_portfolio_tags($printTags = true)
{
	global $post;
	$tagsContent = "<div class='atc_dp_portfolio-tags-container'>";

	$tagsContent .= "<p class='atc_dp_portfolio-tags'>";
//	$tagsContent .= "<span class='portfolio-tags-title'>Languages & Technologies</span><br>";
	$platforms = get_the_terms($post, 'atc_dp_platforms');
	if (!empty($platforms))
	{
		foreach ($platforms as $platform)
			$tagsContent .= "<span class='atc_dp_portfolio-tag atc_dp_portfolio-tag-platform'>$platform->name</span> ";
	}

	$projectURL = get_post_meta($post->ID, 'atc_dp_project_URL');
	if (!empty($projectURL))
	{
		$tagsContent .= "<br><a class='atc_dp_portfolio-tag atc_dp_portfolio-tag-projecturl' href='$projectURL[0]'>$projectURL[0]</a>";
	}
	// Append the langiages tags
	$languages = get_the_terms($post, 'atc_dp_languages');
	if (!empty($languages))
	{
		$tagsContent .= "<br>";
		foreach ($languages as $language)
			$tagsContent .= "<span class='atc_dp_portfolio-tag atc_dp_portfolio-tag-language'>$language->name</span> ";
	}
//	$tagsContent .= "</p>";


//	$tagsContent .= "<p class='portfolio-tags'>";

	// Append the tools tags
	$tools = get_the_terms($post, 'atc_dp_tools');
	if (!empty($tools))
	{
		$tagsContent .= "<br>";
//		$tagsContent .= "<span class='portfolio-tags-title'>Technologies</span><br>";
		foreach ($tools as $tool)
			$tagsContent .= "<span class='atc_dp_portfolio-tag atc_dp_portfolio-tag-tools'>$tool->name</span> ";
	}



	$tagsContent .= "</p></div>";



	if ($printTags == true)
		echo $tagsContent;
	else
		return $tagsContent;



}

?>
