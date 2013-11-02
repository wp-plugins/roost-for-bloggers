<?php
	global $wpdb;
	$table = $wpdb->prefix . "roostsettings";
	$sql = "SELECT * FROM " . $table . " where 1";
	$results = $wpdb->get_results($sql);

	if (count($results) > 0) {
	    foreach ($results as $result) {

			if (strlen($result->appkey) > 0) {
				$activeKey = true;
			} else {
				$activeKey = false;
			}
?>

<div id="rooster">
	<div id="banner">
		<div class="roost-wrapper">
			<?php if($activeKey){ ?>
				<div id="roostHeaderRight">	
					<form action="" method="post">
						<input type="Submit" id="roostLogOut" class="type-submit" name="clearkey" value="Log Out" />
					</form>
					<span id="roostUsername"><?php echo $result->username ?></span>
				</div>
			<?php } ?>
			<img src="<?php echo ROOST_URL; ?>layout/images/roost_logo.png" alt="" />
		</div>
	</div>
	<?php		
		if(isset($status)){
	?>
		<div id="rooster-status"><?php echo($status); ?><span id="rooster-status-close">x</span></div>
		
	<?php } ?>
		<div id="roost-main-wrapper">
		    <form action="" method="post">		    	
		    	<!--BEGIN USER LOGIN SECTION-->
				<?php if(!$activeKey){ ?>
				<div class="roost-primary-wrapper roost-login-account">
					<div class="roost-primary-heading">
						<span class="roost-primary-cta">Log in to your Roost account</span>
						<span class="roost-scondary-cta">If you don’t have a Roost account <a href="https://get.roost.me/signup?returnURL=<?php echo admin_url('admin.php?page=roost-for-bloggers/roost.php'); ?>&websiteURL=<?php echo site_url(); ?>" target="_blank">sign up for free!</a></span>
					</div>
		    		<div class="roost-section-content">
	    				<!--USER NAME-->
	    				<span class="roost-label">Username / Email:</span>
	    				<input name="roostuserlogin" type="text" class="type-text roost-control-login" value="<?php echo isset($_POST['roostuserlogin']) ? $_POST['roostuserlogin'] : '' ?>" size="50" placeholder="Enter Your Username or Email"/>
	    				
	    				<!--PASSWORD-->
	    				<span class="roost-label">Password:</span>
	    				<input name="roostpasslogin" type="password" class="type-text roost-control-login" value="<?php echo isset($_POST['roostpasslogin']) ? $_POST['roostpasslogin'] : '' ?>" size="50" placeholder="Enter Your Password"/>
	    				<?php if(isset($roostSites)){ ?>
	    					<!--CONFIGS-->
	    					<span class="roost-label">Choose one of your site configurations to use:wu</span>
	    					
	    					<select id="roostsites" name="roostsites" class="roost-site-select">
	    						<option value="none" selected="selected">-- Choose Site --</option>
	    						<?php  
	    							for($i = 0; $i < count($roostSites); $i++) {
	    						?>
	    							<option value="<?php echo $roostSites[$i]['key'] . '|' . $roostSites[$i]['secret']; ?>"><?php echo $roostSites[$i]['name']; ?></option>
	    						<?php 
	    							}
	    						?>
	    					</select>
		    				<span class="roostDisclaimer">
			    				To switch to a differrent configuration after you log in, you will need to log out and choose a different configuration.
							</span>
	    				<?php } ?>				
		    		</div>
		    		<div class="roost-primary-footer">
		    			<input type="Submit" class="type-submit" id="roost-middle-save" name="<?php echo isset($roostSites) ? 'roostconfigselect' : 'roostlogin' ?>" value="<?php echo isset($roostSites) ? 'Choose Site' : 'Login' ?>" />
		    		</div>
		    	</div>	
		    	<?php } ?>
		    	<!--END USER LOGIN SECTION-->
		    	

				<!--BEGIN SETTINGS SECTION-->
				<?php if($activeKey){ ?>
				<div class="roost-section-wrapper">
					<span class="roost-section-heading">Settings</span>
					<span class="roost-section-expansion"></span>
					<div class="roost-section-content">
						<div class="roost-no-collapse">	
							<div id="roost-block">
								<div class="roost-settings-tri">
									<span class="roost-label">Auto Push:<span class="roost-hint" data-helper="autopush">?</span></span>
									<select id="autopush" name="autopush" class="roost-control-secondary">
										<option value="1" <?php echo ($result->autopush == 1 ? "selected='selected'" : "") ?>>Enabled</option>
										<option value="0" <?php echo ($result->autopush == 0 ? "selected='selected'" : "") ?>>Disabled</option>
									</select>
								</div>
								<div class="roost-settings-tri">
									<span class="roost-label">Use Custom Text:<span class="roost-hint" data-helper="customtext">?</span></span>
									<select id="usecustomtext" name="usecustomtext" class="roost-control-secondary">
										<option value="1" <?php echo ($result->usecustomtext == 1 ? "selected='selected'" : "") ?>>Yes</option>
										<option value="0" <?php echo ($result->usecustomtext == 0 ? "selected='selected'" :"") ?>>No</option>
									</select>
									<br />
									<input name="customtext" type="text" class="type-text roost-control-secondary" value="<?php echo $result->customtext ?>" size="50" placeholder="Enter Custom Text"/>
									<br />
									<span class="roost-label">Custom Text Position:<span class="roost-hint" data-helper="customtextposition">?</span></span>
									<select id="textposition" name="textposition" class="roost-control-secondary">
										<option value="1" <?php echo ($result->textposition == 1 ? "selected='selected'" : "") ?>>Before</option>
										<option value="0" <?php echo ($result->textposition == 0 ? "selected='selected'" : "") ?>>After</option>
									</select>
								</div>
								<div class="roost-settings-tri">
									<span class="roost-label">Registration:<span class="roost-hint" data-helper="registration">?</span></span>	
									<select id="appusage" name="appusage" onChange="roost_reg_modeChange()" class="roost-control-secondary">
										<option value="1" <?php echo ($result->appusage == 1 ? "selected='selected'" : "") ?>>Roost Header Bar</option>
										<option value="2" <?php echo ($result->appusage == 2 ? "selected='selected'" : "") ?>>Roost Footer Bar</option>
										<option value="3" <?php echo ($result->appusage == 3 ? "selected='selected'" : "") ?>>Custom Registration Code</option>
										<option value="0" <?php echo ($result->appusage == 0 ? "selected='selected'" : "") ?>>No Registration</option>
									</select>
									<div id="custom-bar-text"> 
										<span class="roost-label">Custom Bar Text:<span class="roost-hint" data-helper="custombartext">?</span></span>		
										<textarea id="" name="custombartext" class="roost-control-secondary rooster-custom-script"><?php echo $result->custombartext ?></textarea>
									</div>
									<div id="custom-code"> 
										<span class="roost-label">Custom Code:<span class="roost-hint" data-helper="customreg">?</span></span>		
										<textarea id="" name="custommsg" class="roost-control-secondary rooster-custom-script"><?php echo $result->custommsg ?></textarea>
									</div>
								</div>
							</div>
							<hr />
							<input type="Submit" class="type-submit roost-control-secondary" id="roost-middle-save" name="savesettings" value="Save Settings" />
						</div>
					</div>
				</div>
				<?php } ?>
				<!--END SETTINGS SECTION-->
				<!--BEGIN MANUAL PUSH SECTION-->
				<?php if($activeKey){ ?>
				<div class="roost-section-wrapper">
					<span class="roost-section-heading">Send Manual Alert:</span>
					<span class="roost-section-expansion"></span>
					<div class="roost-section-content" id="roost-manual-send-section">
						<div class="roost-no-collapse">
							<div id="roost-manual-send-wrapper">
								<div class="roost-send-title roost-title-active" id="roost-send-title-first" data-related="1">
									Message with a content link
								</div>
								<div class="roost-send-title" id="roost-send-title-second" data-related="2">
									Message only
								</div>
								<div class="roost-send-type roost-send-active" id="roost-send-with-link" data-related="1">	
									<div class="roost-input-text">
										<span class="roost-label">Message:<span class="roost-hint" data-helper="message">?</span></span>
										<input name="manualtext" type="text" class="type-text roost-control-secondary" value="" size="50" placeholder="Enter Message"/><br />
									</div>
									<div class="roost-input-text">
										<span class="roost-label">Link:<span class="roost-hint" data-helper="messagelink">?</span></span>
										<input name="manuallink" type="text" class="type-text roost-control-secondary" value="" size="50" placeholder="Enter URL"/>
									</div>
									<input type="Submit" class="type-submit roost-control-secondary" name="manualpush" id="manualpush" value="Send"/>
								</div>
								<div class="roost-send-type" id="roost-send-no-link" data-related="2">	
									<div class="roost-input-text">
										<span class="roost-label">Message:<span class="roost-hint" data-helper="message">?</span></span>
										<input name="manualtext2" type="text" class="type-text roost-control-secondary" value="" size="50" placeholder="Enter Message"/><br />
									</div>
									<input type="Submit" class="type-submit roost-control-secondary" name="manualpush2" id="manualpush2" value="Send"/>
								</div>

							</div>
						</div>
					</div>
				</div>
		    	<?php } ?>
				<!--END MANUAL PUSH SECTION-->	
			</form>
		</div>
		<div id="roost-footer">
			&copy; <?php echo date("Y"); ?> Notice Software
		</div>
	
	<?php
	    }
	}
	?>
	
	<script>
		<?php if(isset($roostSites)){ ?>
			jQuery(".roost-control-login").attr("disabled", "disabled");
		<?php } ?>
		
		<?php if($activeKey){ ?>
			jQuery(".roost-control-primary").attr("disabled", "disabled");
		<?php } else { ?>
			jQuery(".roost-control-secondary").attr("disabled", "disabled");		
		<?php } ?>
		
		
		<?php if(!$activeKey){ ?>
			jQuery('.roost-wrapper').css('width', '90px');
		<?php } ?>
		
		
		<?php if($activeKey){ ?>
						
			function roost_reg_modeChange() {
			    var eSelect = document.getElementById("appusage");
			    var customCode = document.getElementById("custom-code");
			    var customBarText = document.getElementById("custom-bar-text");
			    var id = eSelect.value;
				if (id == "1" || id == "2") {
				    customBarText.style.display = "block"
				} else {
				    customBarText.style.display = "none";
				}
			    if (id == "3") {
			        customCode.style.display = "block"
			    } else {
			        customCode.style.display = "none";
			    }
			}
			//run it once to get the right state
			roost_reg_modeChange();
			
			var manBtn = document.getElementById("manualpush");
			var manBtn2 = document.getElementById("manualpush2");
			var confirmIt = function (e) {
			    if (!confirm("Are you sure you would like to send a notification?")) e.preventDefault();
			};
			manBtn.addEventListener("click", confirmIt, false);
			manBtn2.addEventListener("click", confirmIt, false);
		<?php } ?>	
	
		var hintText = {
			autopush: 'When enabled, a push notification will automatically be sent to your subscribers when you publish a post.',
			customtext: 'You can add a message to your auto-pushed notifications by enabling this option.',
			customtextposition: 'This will be where your custom message text appears.',
			registration: 'Choose an pre-built method to allow user registrations or write a custom registration script.',
			customreg: 'This code will be inserted into your pages and posts. Make sure to include &#60script&#62 tags if needed.',
			custombartext: 'Customize the message on the Roost Header or Footer Bar.',
			message: 'Write your message to send here.',
			messagelink: 'This will be the link that a person is sent to when they tap on your notification.',
			password: 'Enter a password for you Roost account. Make it good. Must be at least 6 characters and contain at least one number or special character such as @ or !'
		}
					  
		jQuery('li.wp-not-current-submenu#toplevel_page_roost-roost .wp-menu-image img').attr('src', '<?php echo ROOST_URL . "/layout/images/"?>roost_thumb_unselected.png');

		jQuery('li.current#toplevel_page_roost-roost .wp-menu-image img').attr('src', '<?php echo ROOST_URL . "/layout/images/"?>roost_thumb_selected.png');
		
		jQuery('#roost-send-title-second').hide();
	</script>
</div>