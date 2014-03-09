<?php
	$roostSettings = get_option('roost_settings');
		if (strlen($roostSettings['appKey']) > 0) {
			$activeKey = true;
		} else {
			$activeKey = false;
		}
?>
<div id="rooster">
    <div id="roostHeader">
        <?php if($activeKey){ ?>
            <div class="roost-wrapper">
                <div id="roostHeaderRight">	
                    <form action="" method="post">
                        <input type="Submit" id="roostLogOut" class="type-submit" name="clearkey" value="Log Out" />
                    </form>
                    <span id="roostUsername">
                        <span id="roostUserLogo">
                            <?php if ($roostServerSettings['hasLogo'] == true) { ?>    
                                <img src="http://get.roost.me/api/device/logo?appKey=<?php echo($roostSettings['appKey']); ?>" />
                            <?php } else { ?>
                                <img src="<?php echo ROOST_URL; ?>layout/images/roost-icon-25.png" />
                            <?php } ?>
                            <span class="roostTip">This is your Roost account logo. It will be shown to your users at the time of registration and when notifications are sent. You can set it by visiting the Roost dashboard.</span>

                        </span>
                        <?php echo $roostSettings['username'] ?>
                    </span>
                </div>
                <img src="<?php echo ROOST_URL; ?>layout/images/roost-red-logo.png" />
            </div>
        <?php } ?>
    </div>
    <?php		
		if(isset($status)){
	?>
		<div id="rooster-status"><span id="rooster-status-text"><?php echo($status); ?></span><span id="rooster-status-close">Dismiss</span></div>
		
	<?php } ?>
		<div id="roost-main-wrapper">
		    <form action="" method="post">		    	
		    	<!--BEGIN USER LOGIN SECTION-->
				<?php if(!$activeKey){ ?>
				<div class="roost-primary-wrapper roost-login-account">
					<div id="roost-primary-logo">
						<img src="<?php echo ROOST_URL; ?>layout/images/roost_logo.png" alt="" />
					</div>
					<div class="roost-primary-heading">
						<span class="roost-primary-cta">Welcome! Log in to your Roost account below.</span>
						<span class="roost-secondary-cta">If you don’t have a Roost account <a href="https://get.roost.me/signup?returnURL=<?php echo admin_url('admin.php?page=roost-for-bloggers/roost.php'); ?>&websiteURL=<?php echo site_url(); ?>&source=wpplugin" target="_blank">sign up for free!</a></span>
					</div>
		    		<div class="roost-section-content">
	    				<!--USER NAME-->
						<div class="roost-login-input">
		    				<span class="roost-label">Email:</span>
		    				<input name="roostuserlogin" type="text" class="type-text roost-control-login" value="<?php echo isset($_POST['roostuserlogin']) ? $_POST['roostuserlogin'] : '' ?>" size="50" />
		    			</div>
		    			<div class="roost-login-input">
		    				<!--PASSWORD-->
		    				<span class="roost-label">Password:</span>
		    				<input name="roostpasslogin" type="password" class="type-text roost-control-login" value="<?php echo isset($_POST['roostpasslogin']) ? $_POST['roostpasslogin'] : '' ?>" size="50" />
		    			</div>
	    				<?php if(isset($roostSites)){ ?>
	    					<!--CONFIGS-->
	    					<div class="roost-login-input">
	    					
		    					<span class="roost-label">Choose a configurations to use:</span>
		    					
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
				    				To switch configurations after you log in, you will need to log out and choose a different configuration.
								</span>
							</div>
	    				<?php } ?>				
		    		</div>
		    		<div class="roost-primary-footer">
		    			<input type="Submit" class="type-submit" id="roost-middle-save" name="<?php echo isset($roostSites) ? 'roostconfigselect' : 'roostlogin' ?>" value="<?php echo isset($roostSites) ? 'Choose Site' : 'Login' ?>" />
		    			<span class="left-link"><a href="https://get.roost.me/login?forgot=true" target="_blank">forget password?</a></span>
		    		</div>
		    	</div>	
		    	<?php } ?>
		    	<!--END USER LOGIN SECTION-->
     
				<!--BEGIN RECENT ACTIVITY SECTION-->
				<?php if($activeKey){ ?>
				<div class="roost-section-wrapper">
					<span class="roost-section-heading">Recent activity</span>
					<span class="roost-section-expansion"></span>
					<div class="roost-section-content roost-section-secondary" id="roost-recent-activity">
						<div class="roost-no-collapse">
                            <div class="roostStats">
                            </div>
						</div>
					</div>
				</div>
		    	<?php } ?>
				<!--END RECENT ACTIVITY SECTION-->                
                
				<!--BEGIN ALL TIME STATS SECTION-->
				<?php if($activeKey){ ?>
				<div class="roost-section-wrapper">
					<span class="roost-section-heading">All-time stats</span>
					<span class="roost-section-expansion"></span>
					<div class="roost-section-content" id="roost-all-stats">
						<div class="roost-no-collapse">
                            <div class="roostStats">
                                <?php if ( $roostStats['messages'] > 4 ) { ?>
                                    <script>jQuery('#roost-all-stats').css('background-image', 'none');</script>
                                    <?php 
                                        if ( $roostStats['pageViewCount'] != 0 ) {
                                            $avgTimeOnSite = round($roostStats['timeOnSite'] / $roostStats['pageViewCount']);
                                        } else {
                                            $avgTimeOnSite = "0";   
                                        }
                                    ?>
                                    <div class="roost-stats-metric">
                                        <span class="roost-stat"><?php echo(number_format($roostStats['registrations'])); ?></span>
                                        <span class="roost-stat-label">Total subscribers</span>                                  
                                    </div>
                                    <div class="roost-stats-metric">
                                        <span class="roost-stat"><?php echo(number_format($roostStats['messages'])); ?></span>
                                        <span class="roost-stat-label">Notifications Sent</span>                                  
                                    </div>
<!--
                                    <div class="roost-stats-metric">
                                        <span class="roost-stat"><?php echo(number_format($roostStats['messages'])); ?></span>
                                        <span class="roost-stat-label">Notification deliveries</span>                                  
                                    </div>
-->
<!--
                                    <div class="roost-stats-metric">$roostStats['timeOnSite']
                                        <span class="roost-stat"><?php echo(number_format($avgTimeOnSite)); ?><span id="roost-time-label"> mins</span></span>
                                        <span class="roost-stat-label">Average time-on-site</span>                                  
                                    </div>
-->
                                    <div class="roost-stats-metric">
                                        <span class="roost-stat"><?php echo(number_format(($roostStats['timeOnSite'])/60000)); ?><span id="roost-time-label"> mins</span></span>
                                        <span class="roost-stat-label">Total time-on-site</span>                                  
                                    </div>

                                    <div class="roost-stats-metric">
                                        <span class="roost-stat"><?php echo(number_format($roostStats['pageViewCount'])); ?></span>
                                        <span class="roost-stat-label">Total page views</span>                                  
                                    </div>
                                    <?php if (strlen($roostStats['messages']) > 5 || strlen($avgTimeOnSite) > 4 || strlen($roostStats['timeOnSite']) > 4) { ?>
                                        <script>jQuery('.roost-stat').css('font-size', '50px');</script>
                                    <?php } ?>
                                <?php } ?>
                            </div>
						</div>
					</div>
				</div>
		    	<?php } ?>
				<!--END ALL TIME STATS SECTION-->
                
				<!--BEGIN MANUAL PUSH SECTION-->
				<?php if($activeKey){ ?>
				<div class="roost-section-wrapper">
					<span class="roost-section-heading">Send a manual push notification</span>
					<span class="roost-section-expansion"></span>
					<div class="roost-section-content roost-section-secondary" id="roost-manual-send-section">
						<div class="roost-no-collapse">
							<div id="roost-manual-send-wrapper">
								<div class="roost-send-type roost-send-active" id="roost-send-with-link" data-related="1">	
									<div class="roost-input-text">
										<div class="roost-label">Notification text:</div>
										<div class="roost-input-wrapper">
                                            <span id="roostManualNoteCount">70</span>
                                            <input name="manualtext" type="text" class="type-text roost-control-secondary" id="roostManualNote" value="" size="50" />
                                            <span class="roost-input-caption">Enter the text for the notification you would like to send your subscribers.</span>
                                        </div>
									</div>
									<div class="roost-input-text">
										<div class="roost-label">Notification link:</div>
										<div class="roost-input-wrapper">
                                            <input name="manuallink" type="text" class="type-text roost-control-secondary" value="" size="50" />
                                            <span class="roost-input-caption">Enter a website link (URL) that your subscribers will be sent to upon clicking the notification. <!-- If you are just sending a message, leave this field blank. --></span>

                                        </div>
                                    </div>
									<input type="Submit" class="type-submit roost-control-secondary" name="manualpush" id="manualpush" value="Send notification"/>
								</div>
							</div>
						</div>
					</div>
				</div>
		    	<?php } ?>
				<!--END MANUAL PUSH SECTION-->	

				<!--BEGIN SETTINGS SECTION-->
				<?php if($activeKey){ ?>
				<div class="roost-section-wrapper">
					<span class="roost-section-heading">Settings</span>
					<span class="roost-section-expansion"></span>
					<div class="roost-section-content roost-section-secondary">
						<div class="roost-no-collapse">	
							<div id="roost-block">
                                <div class="roost-setting-wrapper">

                                    <span class="roost-label">Auto Push:</span>
                                    <input type="checkbox" name="autoPush" class="roost-control-secondary" value="1" <?php if(!empty($roostSettings['autoPush'])){ echo('checked');} ?> />
                                    <span class="roost-setting-caption">Enabling this will automatically send a push notification to your subscribers every time you publish a new post.</span>
                                </div>
                                <div class="roost-setting-wrapper">

                                    <span class="roost-label">Mobile push support:</span>
                                    <input type="checkbox" name="mobilePush" class="roost-control-secondary" value="1" <?php if($roostServerSettings['roostBarSetting'] == 'TOP' || $roostServerSettings['roostBarSetting'] == 'BOTTOM' ){ echo('checked');} ?> />
                                    <span class="roost-setting-caption">Enabling this will allow your readers to subscribe and recieve push notifications on their phone or tablet when they view your mobile site.
First-time subscribers will be prompted to install the iOS or Android Roost app in order to recieve notifications.</span>

                                </div>
                                <div class="roost-setting-wrapper">
                                    <span class="roost-label">Activate all Roost features:</span>
								    <input type="checkbox" name="autoUpdate" class="roost-control-secondary" value="1" <?php if($roostServerSettings['autoUpdate'] == true ){ echo('checked');} ?> />
                                    <span class="roost-setting-caption">Enabling this will automatically activate current and future features as they are added to the plugin.</span>

                                </div>
                                <input type="Submit" class="type-submit roost-control-secondary" id="roost-middle-save" name="savesettings" value="Save Settings" />                              
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
				<!--END SETTINGS SECTION-->
			</form>
            <div id="roostSupportTag">Have Questions, Comments, or Need a Hand? Hit us up at <a href="mailto:support@roost.me" target="_blank">support@roost.me</a> We're Here to Help.</div>
		</div>
	<script>
		<?php if(isset($roostSites)){ ?>
			jQuery(".roost-control-login").attr("disabled", "disabled");
		<?php } ?>		
		<?php if($activeKey){ ?>
			var manBtn = document.getElementById("manualpush");
			var confirmIt = function (e) {
			    if (!confirm("Are you sure you would like to send a notification?")) e.preventDefault();
			};
			manBtn.addEventListener("click", confirmIt, false);
		<?php } ?>
	</script>
</div>