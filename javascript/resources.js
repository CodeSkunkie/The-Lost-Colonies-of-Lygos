function refresh_resources_display()
{
	// TODO: implement
}

function resource_bundle_html(resource_bundle)
{
	var img_src_pt1 = "media/themes/"+ theme +"/images/";
	var img_src_pt2 = "_mini.png"
	var return_html = "";
	for ( var field in resource_bundle )
	{
		return_html += "<img src=\""+ img_src_pt1 + field + img_src_pt2 +"\" />:";
		return_html += resource_bundle[field] +" ";
	}
	
	return return_html;
}

function resource_upkeep_html(resource_bundle)
{
	var img_src_pt1 = "media/themes/"+ theme +"/images/";
	var img_src_pt2 = "_mini.png"
	var return_html = "";
	for ( var field in resource_bundle )
	{
		if ( resource_bundle[field] != 0 )
		{
			return_html += "<img src=\""+ img_src_pt1 + field + img_src_pt2 +"\" />:";
			return_html += resource_bundle[field] +" ";
		}
	}
	
	return return_html;
}