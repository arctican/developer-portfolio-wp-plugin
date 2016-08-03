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
function register_portfolio_styles()
{
    wp_register_style( 'portfolio-style', plugins_url( '../style.css', __FILE__ ));
    wp_enqueue_style( 'portfolio-style' );
}
add_action( 'wp_enqueue_scripts', 'register_portfolio_styles' );



/** Hook into content filter to show the tags */
function my_the_content_filter($content)
{

	if (get_post_type() == 'projects' && is_single())
  		return render_portfolio_tags(false) . "<hr>" . $content;
	else
		return $content;
}
add_filter( 'the_content', 'my_the_content_filter' );



/** Renders the portfolio tags */
function render_portfolio_tags($printTags = true)
{
	global $post;
	$tagsContent = "<div class='portfolio-tags-container'>";

	$tagsContent .= "<p class='portfolio-tags'>";
//	$tagsContent .= "<span class='portfolio-tags-title'>Languages & Technologies</span><br>";
	$platforms = get_the_terms($post, 'platforms');
	if (!empty($platforms))
	{
		foreach ($platforms as $platform)
			$tagsContent .= "<span class='portfolio-tag portfolio-tag-platform'>$platform->name</span> ";
	}

	$projectURL = get_post_meta($post->ID, 'ATC_DP_project_URL');
	if (!empty($projectURL))
	{
		$tagsContent .= "<br><a class='portfolio-tag portfolio-tag-projecturl' href='$projectURL[0]'>$projectURL[0]</a>";
	}
	// Append the langiages tags
	$languages = get_the_terms($post, 'languages');
	if (!empty($languages))
	{
		$tagsContent .= "<br>";
		foreach ($languages as $language)
			$tagsContent .= "<span class='portfolio-tag portfolio-tag-language'>$language->name</span> ";
	}
//	$tagsContent .= "</p>";


//	$tagsContent .= "<p class='portfolio-tags'>";

	// Append the tools tags
	$tools = get_the_terms($post, 'tools');
	if (!empty($tools))
	{
		$tagsContent .= "<br>";
//		$tagsContent .= "<span class='portfolio-tags-title'>Technologies</span><br>";
		foreach ($tools as $tool)
			$tagsContent .= "<span class='portfolio-tag portfolio-tag-tools'>$tool->name</span> ";
	}



	$tagsContent .= "</p></div>";



	if ($printTags == true)
		echo $tagsContent;
	else
		return $tagsContent;



}

?>
