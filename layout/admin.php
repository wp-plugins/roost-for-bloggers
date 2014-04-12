<?php
    $roost = new Roost();
    $roost_settings = get_option('roost_settings');
    $site_url = $roost->site_url();
    if (strlen($roost_settings['appKey']) > 0) {
        $roost_active_key = true;
        $roost = new Roost;
    } else {
        $roost_active_key = false;
    }
?>
<div id="rooster">
    <div id="roostHeader">
        <?php if($roost_active_key){ ?>
            <div class="roost-wrapper">
                <div id="roostHeaderRight">	
                    <form action="" method="post">
                        <input type="Submit" id="roostLogOut" class="type-submit" name="clearkey" value="Log Out" />
                    </form>
                    <span id="roostUsername">
                        <span id="roostUserLogo">
                            <?php if ($roost_server_settings['hasLogo'] == true) { ?>    
                                <img src="http://get.roost.me/api/device/logo?appKey=<?php echo($roost_settings['appKey']); ?>" />
                            <?php } else { ?>
                                <img src="<?php echo ROOST_URL; ?>layout/images/roost-icon-25.png" />
                            <?php } ?>
                            <span class="roostTip">This is your Roost account logo. It will be shown to your users at the time of registration and when notifications are sent. You can set it by visiting the Roost dashboard.</span>

                        </span>
                        <?php echo $roost_settings['username'] ?>
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
				<?php if(!$roost_active_key){ ?>
                <div id="roost-login-wrapper">
                    <?php if(empty($roost_sites)){ ?>
                        <div id="roost-signup-wrapper">
                            <div id="roost-signup-inner">
                                <img src="<?php echo ROOST_URL; ?>layout/images/roost_logo.png" alt="Roost Logo" />
                                <h2>Create a free account</h2>
                                <p>
                                    Welcome! Creating an account only takes a few seconds and will give you access 
                                    to additional features like our analytics dashboard at roost.me
                                </p>
                                <a href="https://get.roost.me/signup?returnURL=<?php echo admin_url('admin.php?page=roost-for-bloggers/includes/roost-core.php'); ?>&websiteURL=<?php echo $site_url; ?>&source=wpplugin" target="_blank" id="roost-create-account" class="roost-signup"><img src="<?php echo ROOST_URL; ?>layout/images/roost-arrow-white.png" />Create an account</a>
                                <div id="roost-bottom-right">Already have an account? <span class="roost-signup">Sign in</span></div>
                            </div>
                        </div>
            		<?php } ?>
                    <div id="roost-signin-wrapper" class="roost-login-account">
                        <div id="roost-primary-logo">
                            <img src="<?php echo ROOST_URL; ?>layout/images/roost_logo.png" alt="" />
                        </div>
                        <div class="roost-primary-heading">
                            <span class="roost-primary-cta">Welcome! Log in to your Roost account below.</span>
                            <span class="roost-secondary-cta">If you don’t have a Roost account <a href="https://get.roost.me/signup?returnURL=<?php echo admin_url('admin.php?page=roost-for-bloggers/includes/roost-core.php'); ?>&websiteURL=<?php echo $site_url; ?>&source=wpplugin" target="_blank" class="">sign up for free!</a></span>
                        </div>
                        <div class="roost-section-content">
                            <!--USER NAME-->
                            <div class="roost-login-input">
                                <span class="roost-label">Email:</span>
                                <input name="roostuserlogin" type="text" class="type-text roost-control-login" value="<?php echo isset($_POST['roostuserlogin']) ? $_POST['roostuserlogin'] : '' ?>" size="50" tabindex="1" />
                            </div>
                            <div class="roost-login-input">
                                <!--PASSWORD-->
                                <span class="roost-label">Password:</span>
                                <input name="roostpasslogin" type="password" class="type-text roost-control-login" value="<?php echo isset($_POST['roostpasslogin']) ? $_POST['roostpasslogin'] : '' ?>" size="50" tabindex="2" />
                            </div>
                            <?php if(isset($roost_sites)){ ?>
                                <!--CONFIGS-->
                                <div class="roost-login-input">

                                    <span class="roost-label">Choose a configurations to use:</span>

                                    <select id="roostsites" name="roostsites" class="roost-site-select">
                                        <option value="none" selected="selected">-- Choose Site --</option>
                                        <?php  
                                            for($i = 0; $i < count($roost_sites); $i++) {
                                        ?>
                                            <option value="<?php echo $roost_sites[$i]['key'] . '|' . $roost_sites[$i]['secret']; ?>"><?php echo $roost_sites[$i]['name']; ?></option>
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
                            <input type="Submit" class="type-submit" id="roost-middle-save" name="<?php echo isset($roost_sites) ? 'roostconfigselect' : 'roostlogin' ?>" value="<?php echo isset($roost_sites) ? 'Choose Site' : 'Login' ?>" tabindex="3" />
                            <?php submit_button( 'Cancel', 'delete', 'cancel', false, array( 'tabindex' => '4' ) ); ?>
                            <span class="left-link"><a href="https://get.roost.me/login?forgot=true" target="_blank">forget password?</a></span>
                        </div>
                    </div>
                </div>
		    	<?php } ?>
		    	<!--END USER LOGIN SECTION-->
     
				<!--BEGIN RECENT ACTIVITY SECTION-->
				<?php if($roost_active_key){ ?>
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
				<?php if($roost_active_key){ ?>
				<div class="roost-section-wrapper">
					<span class="roost-section-heading">All-time stats*</span>
					<span class="roost-section-expansion"></span>
					<div class="roost-section-content" id="roost-all-stats">
						<div class="roost-no-collapse">
                            <div class="roostStats">
                                <?php if ( $roost_stats['messages'] > 4 ) { ?>
                                    <script>jQuery('#roost-all-stats').css('background-image', 'none');</script>
                                    <?php 
                                        if ( $roost_stats['pageViewCount'] != 0 ) {
                                            $avgTimeOnSite = round($roost_stats['timeOnSite'] / $roost_stats['pageViewCount']);
                                        } else {
                                            $avgTimeOnSite = "0";   
                                        }
                                    ?>
                                    <div class="roost-stats-metric">
                                        <span class="roost-stat"><?php echo(number_format($roost_stats['registrations'])); ?></span>
                                        <span class="roost-stat-label">Total subscribers</span>                                  
                                    </div>
                                    <div class="roost-stats-metric">
                                        <span class="roost-stat"><?php echo(number_format($roost_stats['messages'])); ?></span>
                                        <span class="roost-stat-label">Notifications Sent</span>                                  
                                    </div>
<!--
                                    <div class="roost-stats-metric">
                                        <span class="roost-stat"><?php echo(number_format($roost_stats['messages'])); ?></span>
                                        <span class="roost-stat-label">Notification deliveries</span>                                  
                                    </div>
-->
<!--
                                    <div class="roost-stats-metric">$roost_stats['timeOnSite']
                                        <span class="roost-stat"><?php echo(number_format($avgTimeOnSite)); ?><span id="roost-time-label"> mins</span></span>
                                        <span class="roost-stat-label">Average time-on-site</span>                                  
                                    </div>
-->
                                    <div class="roost-stats-metric">
                                        <span class="roost-stat"><?php echo(number_format(($roost_stats['timeOnSite'])/60000)); ?><span id="roost-time-label"> mins</span></span>
                                        <span class="roost-stat-label">Total time-on-site</span>                                  
                                    </div>

                                    <div class="roost-stats-metric">
                                        <span class="roost-stat"><?php echo(number_format($roost_stats['pageViewCount'])); ?></span>
                                        <span class="roost-stat-label">Total page views</span>                                  
                                    </div>
                                    <?php if (strlen($roost_stats['messages']) > 5 || strlen($avgTimeOnSite) > 4 || strlen($roost_stats['timeOnSite']) > 4) { ?>
                                        <script>jQuery('.roost-stat').css('font-size', '50px');</script>
                                    <?php } ?>
                                <?php } ?>
                            </div>
						</div>
					</div>
				</div>
                <div id="roostStatsDisclaimer">*Stats are accurate within the past hour. Values are cached on our servers to provide better performnce.</div>
		    	<?php } ?>
				<!--END ALL TIME STATS SECTION-->

				<!--BEGIN MANUAL PUSH SECTION-->
				<?php if($roost_active_key){ ?>
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
                                            <span id="roostManualNoteCount"><span id="roostManualNoteCountInt">0</span> / 70 (reccommended)</span>
                                            <input name="manualtext" type="text" class="type-text roost-control-secondary" id="roostManualNote" value="" size="50" />
                                            <span class="roost-input-caption">Enter the text for the notification you would like to send your subscribers.</span>
                                        </div>
									</div>
									<div class="roost-input-text">
										<div class="roost-label">Notification link:</div>
										<div class="roost-input-wrapper">
                                            <input name="manuallink" type="text" class="type-text roost-control-secondary" value="" size="50" />
                                            <span class="roost-input-caption">Enter a website link (URL) that your subscribers will be sent to upon clicking the notification.</span>

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
				<?php if($roost_active_key){ ?>
				<div class="roost-section-wrapper">
					<span class="roost-section-heading">Settings</span>
					<span class="roost-section-expansion"></span>
					<div class="roost-section-content roost-section-secondary">
						<div class="roost-no-collapse">	
							<div id="roost-block">
                                <div class="roost-setting-wrapper">

                                    <span class="roost-label">Auto Push:</span>
                                    <input type="checkbox" name="autoPush" class="roost-control-secondary" value="1" <?php if(!empty($roost_settings['autoPush'])){ echo('checked');} ?> />
                                    <span class="roost-setting-caption">Enabling this will automatically send a push notification to your subscribers every time you publish a new post.</span>
                                </div>
                                <div class="roost-setting-wrapper">

                                    <span class="roost-label">Mobile push support:</span>
                                    <input type="checkbox" name="mobilePush" class="roost-control-secondary" value="1" <?php if($roost_server_settings['roostBarSetting'] == 'TOP' || $roost_server_settings['roostBarSetting'] == 'BOTTOM' ){ echo('checked');} ?> />
                                    <span class="roost-setting-caption">Enabling this will allow your readers to subscribe and recieve push notifications on their phone or tablet when they view your mobile site.
First-time subscribers will be prompted to install the iOS or Android Roost app in order to recieve notifications.</span>

                                </div>
                                <div class="roost-setting-wrapper">
                                    <span class="roost-label">Activate all Roost features:</span>
								    <input type="checkbox" name="autoUpdate" class="roost-control-secondary" value="1" <?php if($roost_server_settings['autoUpdate'] == true ){ echo('checked');} ?> />
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
		<?php if( isset( $roost_sites ) ){ ?>
			jQuery(".roost-control-login").attr("disabled", "disabled");
		<?php } ?>		
		<?php if( $roost_active_key ){ ?>
			var manBtn = document.getElementById("manualpush");
			var confirmIt = function (e) {
			    if (!confirm("Are you sure you would like to send a notification?")) e.preventDefault();
			};
			manBtn.addEventListener("click", confirmIt, false);
		<?php } ?>
        <?php if( empty( $roost_sites ) ){ ?>
            (function($){
                if( $('#roost-login-wrapper').length ) {
                    var signup = $('#roost-signup-wrapper');
                    var signin = $('#roost-signin-wrapper');

                    signin.hide();

                    $('.roost-signup').on('click', function() {
                        signup.toggle();
                        signin.toggle();
                    });
                }
            })(jQuery);
        <?php } ?>
	</script>
</div>
