

<?php 
require('./wp-blog-header.php');
require('./layout.php');
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->


<p><h3>Illinois Marching Online Videos:</h3><p>
	
	
<p>
<!-- Start of Brightcove Player -->

<div style="display:none">

</div>

<!--
By use of this code snippet, I agree to the Brightcove Publisher T and C
found at http://corp.brightcove.com/legal/terms_publisher.cfm.
-->

<script language="JavaScript" type="text/javascript" src="http://admin.brightcove.com/js/BrightcoveExperiences.js"></script>

<object id="myExperience" class="BrightcoveExperience">
<param name="bgcolor" value="#FFFFFF" />
<param name="width" value="300" />
<param name="height" value="540" />
<param name="playerID" value="51426181001" />
<param name="publisherID" value="51295183001"/>
<param name="isVid" value="true" />
<param name="isUI" value="true" />

</object>

<!-- End of Brightcove Player --> <!--***************End Page Content***************-->
<?php imo_bottom(); ?>
