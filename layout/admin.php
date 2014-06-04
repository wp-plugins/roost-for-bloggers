<?php
    $roost = new Roost();
    $roost_settings = get_option( 'roost_settings' );
    if ( strlen( $roost_settings['appKey'] ) > 0 ) {
        $roost_active_key = true;
        $roost = new Roost;
    } else {
        $roost_active_key = false;
    }
    $bbPress_active = Roost_bbPress::bbPress_active();
?>

<div id="rooster">
    <div id="roost-header">
        <?php if( $roost_active_key ){ ?>
            <div class="roost-wrapper">
                <div id="roost-header-right">	
                    <form action="" method="post">
                        <input type="Submit" id="roostLogOut" class="type-submit" name="clearkey" value="Log Out" />
                    </form>
                    <span id="roost-username">
                        <span id="roostUserLogo">
                            <?php echo get_avatar($roost_server_settings['ownerEmail'], 25 ); ?>
                        </span>
                        <?php
                            echo $roost_server_settings['ownerEmail'];
                        ?>
                    </span>
                </div>
                <img src="<?php echo ROOST_URL; ?>layout/images/roost-red-logo.png" />
                <?php if( $roost_active_key ) { ?>
                    <div id="roost-site-name"><?php echo( $roost_server_settings['name'] ); ?></div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    <?php
		if ( isset( $status ) ){
	?>
		<div id="rooster-status"><span id="rooster-status-text"><?php echo($status); ?></span><span id="rooster-status-close">Dismiss</span></div>
	<?php } ?>
        <!--BEGIN ADMIN TABS-->
        <?php if( $roost_active_key ) { ?>

            <div id="roost-tabs" class="roost-wrapper">
                <ul>
                    <li class="active">Dashboard</li>
                    <li>Send a notification</li>
                    <li>Settings</li>
                </ul>
            </div>
        <?php } ?>
        <!--END ADMIN TABS-->
        <div id="roost-pre-wrap" class="<?php echo( !empty( $roost_active_key ) ? 'roost-white':''); ?>">
            <div id="roost-main-wrapper">
                <form action="" method="post">		    	
                    <!--BEGIN USER LOGIN SECTION-->
                    <?php if( !$roost_active_key ) { ?>
                    <div id="roost-login-wrapper">
                        <?php if( empty( $roost_sites ) ){ ?>
                            <div id="roost-signup-wrapper">
                                <div id="roost-signup-inner">
                                    <img src="<?php echo ROOST_URL; ?>layout/images/roost_logo.png" alt="Roost Logo" />
                                    <h2>Create a free account</h2>
                                    <p>
                                        Welcome! Creating an account only takes a few seconds and will give you access 
                                        to additional features like our analytics dashboard at roost.me
                                    </p>
                                    <a href="<?php echo( Roost::registration_url() ); ?>" id="roost-create-account" class="roost-signin-link"><img src="<?php echo ROOST_URL; ?>layout/images/roost-arrow-white.png" />Create an account</a>
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
                                <span class="roost-secondary-cta">If you don’t have a Roost account <a href="<?php echo( Roost::registration_url() ); ?>" class="roost-signin-link">sign up for free!</a></span>
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
                                <?php if( isset( $roost_sites ) ) { ?>
                                    <!--CONFIGS-->
                                    <div class="roost-login-input">

                                        <span class="roost-label">Choose a configurations to use:</span>

                                        <select id="roostsites" name="roostsites" class="roost-site-select">
                                            <option value="none" selected="selected">-- Choose Site --</option>
                                            <?php  
                                                for($i = 0; $i < count( $roost_sites ); $i++ ) {
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
                                <input type="hidden" id="roost-timezone-offset" name="roost-timezone-offset" value="" />
                                <input type="Submit" class="type-submit" id="roost-middle-save" name="<?php echo isset($roost_sites) ? 'roostconfigselect' : 'roostlogin' ?>" value="<?php echo isset( $roost_sites ) ? 'Choose Site' : 'Login' ?>" tabindex="3" />
                                <?php submit_button( 'Cancel', 'delete', 'cancel', false, array( 'tabindex' => '4' ) ); ?>
                                <span class="left-link"><a href="https://get.roost.me/login?forgot=true" target="_blank">forget password?</a></span>
                            </div>
                            <div id="roost-sso">
                                <div id="roost-sso-text">
                                    Or sign in with
                                </div>
                                <div class="roost-sso-option">
                                    <a href="<?php echo( Roost::login_url( 'FACEBOOK' ) ); ?>" class="roost-sso-link">
                                        <span id="roost-sso-facebook" class="roost-plugin-image">Facebook</span>
                                    </a>
                                </div>
                                <div class="roost-sso-option">  
                                    <a href="<?php echo( Roost::login_url( 'TWITTER' ) ); ?>" class="roost-sso-link"><span id="roost-sso-twitter" class="roost-plugin-image">Twitter</span></a>
                                </div>
                                <div class="roost-sso-option">
                                    <a href="<?php echo( Roost::login_url( 'GOOGLE' ) ); ?>" class="roost-sso-link"><span id="roost-sso-google" class="roost-plugin-image">Google</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <!--END USER LOGIN SECTION-->

                    <!--BEGIN ALL TIME STATS SECTION-->
                    <?php if( $roost_active_key ) { ?>
                        <div id="roost-activity" class="roost-admin-section"><!--BEGIN ROOST ADMIN SECTION-->
                            <div id="roost-all-stats">
                                <div class="roost-no-collapse">
                                    <div class="roostStats">
                                        <div class="roost-stats-metric">
                                            <span class="roost-stat"><?php echo(number_format($roost_stats['registrations'])); ?></span>
                                            <hr />
                                            <span class="roost-stat-label">Total subscribers on <?php echo( $roost_server_settings['name'] ); ?></span>
                                        </div>
                                        <div class="roost-stats-metric">
                                            <span class="roost-stat"><?php echo(number_format($roost_stats['messages'])); ?></span>
                                            <hr />
                                            <span class="roost-stat-label">Total notifications sent to your subscribers</span>
                                        </div>
                                        <div class="roost-stats-metric">
                                            <span class="roost-stat"><?php echo(number_format($roost_stats['read'])); ?></span>
                                            <hr />
                                            <span class="roost-stat-label">Total notifications read by your subscribers</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php } ?>
                    <!--END ALL TIME STATS SECTION-->

                    <!--BEGIN RECENT ACTIVITY SECTION-->
                    <?php if( $roost_active_key ) { ?>
                            <div class="roost-section-wrapper">
                                <div class="roost-section-heading" id="roost-chart-heading">
                                    Recent Activity
                                    <div id="roost-time-period">
                                        <span id="test-id" class="chart-range-toggle chart-reload active"><span class="load-chart" data-type="APP" data-range="DAY">Day</span></span>
                                        <span class="chart-range-toggle chart-reload"><span class="load-chart" data-type="APP" data-range="WEEK">Week</span></span>
<!--
                                        <span class="chart-range-toggle chart-reload"><span class="load-chart" data-type="APP" data-range="MONTH">Month</span></span>
-->
                                    </div>
                                    <div id="roost-metric-options">
                                        <ul>
                                            <li class="chart-metric-toggle chart-reload active"><span class="chart-value" data-value="s">Subscribes</span></li>
                                            <li class="chart-metric-toggle chart-reload"><span class="chart-value" data-value="n">Notifications</span></li>
                                            <li class="chart-metric-toggle chart-reload"><span class="chart-value" data-value="r">Reads</span></li>
                                            <li class="chart-metric-toggle chart-reload"><span class="chart-value" data-value="p">Page Views</li></li>
                                            <li class="chart-metric-toggle chart-reload"><span class="chart-value" data-value="u">Unsubscribes</span></li>
<!--
                                            <li class="chart-metric-toggle chart-reload"><span class="chart-value" data-value="m">Messages</span></li>
-->
<!--
                                            <li class="chart-metric-toggle chart-reload"><span class="chart-value" data-value="pr">Prompts</span></li>
-->
                                        </ul>
                                    </div>
                                </div>
                                <div class="roost-section-content roost-section-secondary" id="roost-recent-activity">
                                    <div class="roost-no-collapse">
                                        <div id="roostchart_dynamic" class="roostStats">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!--END ROOST ADMIN SECTION-->
                    <?php } ?>
                    <!--END RECENT ACTIVITY SECTION-->

                    <!--BEGIN MANUAL PUSH SECTION-->
                    <?php if( $roost_active_key ) { ?>
                        <div id="roost-manual-push" class="roost-admin-section"><!--BEGIN ROOST ADMIN SECTION-->
                            <div class="roost-section-wrapper">
                                <span class="roost-section-heading">Send a manual push notification</span>
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
                                                <input type="Submit" class="type-submit roost-control-secondary" name="manualpush" id="manualpush" value="Send notification" <?php echo $roost_stats['registrations'] === 0 ? 'disabled':'' ?> />
                                                <?php if ( $roost_stats['registrations'] === 0 ) { ?>
                                                    <span id="roostTipNotification">There must be at least one (1) registered user to your site before you can send a notification.</span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!--END ROOST ADMIN SECTION-->
                    <?php } ?>
                    <!--END MANUAL PUSH SECTION-->	

                    <!--BEGIN SETTINGS SECTION-->
                    <?php if( $roost_active_key ) { ?>
                        <div id="roost-settings" class="roost-admin-section"><!--BEGIN ROOST ADMIN SECTION-->
                            <div class="roost-section-wrapper">
                                <span class="roost-section-heading">Settings</span>
                                <div class="roost-section-content roost-section-secondary">
                                    <div class="roost-no-collapse">	
                                        <div id="roost-block">
                                            <div class="roost-setting-wrapper">

                                                <span class="roost-label">Auto Push:</span>
                                                <input type="checkbox" name="autoPush" class="roost-control-secondary" value="1" <?php if(!empty($roost_settings['autoPush'])){ echo('checked');} ?> />
                                                <span class="roost-setting-caption">Automatically send a push notification to your subscribers every time you publish a new post.</span>
                                            </div>
                                            <div class="roost-setting-wrapper">

                                                <span class="roost-label">Mobile push support:</span>
                                                <input type="checkbox" name="mobilePush" class="roost-control-secondary" value="1" <?php if( 'TOP' == $roost_server_settings['roostBarSetting'] || 'BOTTOM' == $roost_server_settings['roostBarSetting'] ){ echo( 'checked' ); } ?> />
                                                <span class="roost-setting-caption">Enabling this will allow your readers to subscribe and recieve push notifications on their phone or tablet when they view your mobile site.
            First-time subscribers will be prompted to install the iOS or Android Roost app in order to recieve notifications.</span>

                                            </div>
                                            <div class="roost-setting-wrapper">
                                                <span class="roost-label">Activate all Roost features:</span>
                                                <input type="checkbox" name="autoUpdate" class="roost-control-secondary" value="1" <?php if( true == $roost_server_settings['autoUpdate'] ){ echo( 'checked' ); } ?> />
                                                <span class="roost-setting-caption">This will automatically activate current and future features as they are added to the plugin.</span>

                                            </div>
                                            <div class="roost-setting-wrapper">
                                                <span class="roost-label">bbPress Push Notifications:</span>
                                                <input type="checkbox" name="bbPress" class="roost-control-secondary" value="1" <?php if( true == $roost_settings['bbPress'] ){ echo( 'checked' ); } ?> <?php echo( !empty( $bbPress_active['present'] ) ? '':'disabled' ); ?> />
                                                <span class="roost-setting-caption">Extends subscriptions for bbPress forums, topics, and replies to allow subscribing via push notifications if site is viewed in a push capable browser.</span>

                                            </div>

                                            <input type="Submit" class="type-submit roost-control-secondary" id="roost-middle-save" name="savesettings" value="Save Settings" />                              
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!--END ROOST ADMIN SECTION-->
                    <?php } ?>
                    <!--END SETTINGS SECTION-->
                </form>
                <div id="roostSupportTag">Have Questions, Comments, or Need a Hand? Hit us up at <a href="mailto:support@roost.me" target="_blank">support@roost.me</a> We're Here to Help.</div>
            </div>
        </div>
	<script>
        (function($){
            $('#rooster-status-close').click(function() {
                $('#rooster-status').css('display', 'none');
            });

            var timeZoneOffset = new Date().getTimezoneOffset();
            $('#roost-timezone-offset').val(timeZoneOffset);
            
            <?php if( $roost_active_key ) { ?>
                var chart;
                var data = {
                    type: $('.chart-range-toggle.active span').data('type'),
                    range: $('.chart-range-toggle.active span').data('range'),
                    value: $('.chart-metric-toggle.active span').data('value'),
                    offset: new Date().getTimezoneOffset(),
                    action: 'graph_reload',
                };

                $('.chart-reload').on('click', function(e){
                    e.preventDefault();
                    $("#roostchart_dynamic").html("");

                    data = {
                        type: $('.chart-range-toggle.active span').data('type'),
                        range: $('.chart-range-toggle.active span').data('range'),
                        value: $('.chart-metric-toggle.active span').data('value'),
                        offset: new Date().getTimezoneOffset(),
                        action: 'graph_reload',
                    };

                    graphDataRequest(data);
                });

                function graphDataRequest(data) {
                    $.post(ajaxurl, data, function(response) {
                        var data = $.parseJSON( response );
                        loadGraph(data);
                    });
                }

                function loadGraph(data) {
                    $('roostchart_dynamic').html('');

                    chart = new Morris.Bar({
                        element: 'roostchart_dynamic',
                        data: data,
                        barColors: ["#e25351"],
                        xkey: 'label',
                        ykeys: ['value'],
                        labels: ['Value'],
                        hideHover: 'auto',
                        barRatio: 0.4,
                        xLabelAngle: 20
                    });
                }

                $(window).resize(function() {
                    chart.redraw();
                });

                graphDataRequest(data);
            <?php } ?>
        })(jQuery);
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

