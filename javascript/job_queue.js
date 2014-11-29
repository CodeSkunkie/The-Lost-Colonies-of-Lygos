var jobs = [];

function fetch_jobs_queue()
{
	request_data('job_queue', {"colony_id": colony_id}, function(json_data) {
		// Clear out the outdated jobs if any are there.
		$('#jobs_list_mini').html('');
		
		// Save this job's data for later client-side calculations.
		jobs = json_data.jobs;
		
		// If there are no jobs, display a special message.
		if ( json_data.jobs.length == 0 )
		{
			$('<div/>', {
				"text": "There are currently no active construction jobs."
			}).appendTo('#jobs_list_mini');
		}
		
		// Iterate through the jobs.
		for ( var i in json_data.jobs )
		{
			var job = json_data.jobs[i];
			var percent_completed = 10 * (time() - job.start_time) / (job.duration);
			if ( percent_completed > 100 )
			{
				percent_completed = 100;
			}
			$('<div/>', {
				"id": "job_list_mini_job"+ i,
				"class": "job_list_mini_job",
			}).appendTo('#jobs_list_mini');
			$('<div/>', {
				"class": "job_list_mini_job_title",
				"text": job.product_name
			}).appendTo('#job_list_mini_job'+ i);
			$('<div/>', {
				"id" : "job_progress_bar"+ i,
				"class": "job_progress_bar"
			}).appendTo('#job_list_mini_job'+ i);
			$('<div/>', {
				"id" : "job"+ i +"_progress_bar_inner1",
				"class": "job_progress_bar_inner1",
				"style": "width:"+ percent_completed +"%;",
				"onclick": "javascript:refresh_jobs_queue_offline();"
			}).appendTo('#job_progress_bar'+ i);
		}
	});
	
}

function refresh_jobs_queue_offline()
{
	for ( var i in jobs )
	{
		var job = jobs[i];
		var percent_completed = 100 * ((new Date().getTime() - (job.start_time * 1000)) / ((job.completion_time - job.start_time)*1000));
		if ( percent_completed > 100 )
		{
			percent_completed = 100;
			request_data('job_completed', {"job_id": job.id}, function() {
				$("#job_list_mini_job"+ i).hide();
			});
			// Remove this job from the javascript array of jobs.
			jobs.splice(i, 1);
			// Refresh the jobs queue to look for new jobs.
			fetch_jobs_queue();
			// Refresh the visibility of the map after successful scouting mission
			if(job.type == "3" && job.product_type == "2") {
				refresh_map('map');
			}
		}
		$("#job"+ i +"_progress_bar_inner1").css("width", percent_completed +"%");
	}
}

$(function() {
	fetch_jobs_queue();
	
	// Setup an auto-updater for the progress bars that doesn't require 
	// fetching data from the database.
	setInterval(function() {
		refresh_jobs_queue_offline();
	}, 100);
});