<?php

$this->layout = 'promo';

?>

<div style="width:650px; margin:auto; 
	background-color: #202020;
	border-radius: 10px;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px; 
	border:1px solid #3d8fe3;
	padding: 10px 20px;
	margin-top: 25px;">
	<h3>
		Getting Started
	</h3>
	<div style="text-align:left;">
		Contents:
		<ul>
			<li>
				<a href="#construct_module">Constructing a New Module</a>
			</li>
			<li>
				<a href="#resources">Resources</a>
			</li>
		</ul>
	</div>

	<a name="construct_module" />
	<div style="text-align:left">
		<b>Constructing a New Module</b><br />
		Example: constructing the solar array.
		<ol>
			<li>
				Select the colony management option near the bottom of the screen:</br>
				<img src="media/images/quickstart/cm1.png" style="width: 600px;" /><br />
			</li>
			<li>
				select "build new modules":<br />
				<img src="media/images/quickstart/cm2.png" style="width: 600px;" /><br />
			</li>
			<li>
				Locate the module you wish to construct in the list and select "construct module":<br />
				<img src="media/images/quickstart/cm3.png" style="width: 600px;" /><br />
			</li>
			<li>
				This will create a new job, which you can see under "job status". When the progress bar on the job is full, the module has been constructed.<br />
				<img src="media/images/quickstart/cm4.png" style="width: 600px;" /><br />
			</li>
		</ol>
	</div>

	<a name="resources" />
	<div style="text-align:left">
		<b>Resources</b><br />
		<div style="padding-left:50px;">
			Your colony has a limited number of resources that can be spent on various things such as upgrades. The resource quickview bar lists each type of resource in the format of:<br />
			<div style="font-family:Courier; padding-left:50px">
				[amount in stock] / [storage capacity] <br />
				+[hourly production rate]
			</div>
			<img src="media/images/quickstart/resources.png" style="width: 600px;" />
		</div>
	</div>
</div>