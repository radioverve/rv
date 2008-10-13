<div id="player">
        
        <script language="JavaScript" type="text/javascript">

        // Globals
        // Major version of Flash required
        var requiredMajorVersion = 9;
        // Minor version of Flash required
        var requiredMinorVersion = 0;
        // Minor version of Flash required
        var requiredRevision = 0;
        
        // Version check for the Flash Player that has the ability to start Player Product Install (6.0r65)
        var hasProductInstall = DetectFlashVer(6, 0, 65);
        
        // Version check based upon the values defined in globals
        var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
        
        if ( hasProductInstall && !hasRequestedVersion ) {
                // DO NOT MODIFY THE FOLLOWING FOUR LINES
                // Location visited after installation is complete if installation is required
                var MMPlayerType = (isIE == true) ? "ActiveX" : "PlugIn";
                var MMredirectURL = "http://towel.radioverve.com/scripts/";
            document.title = document.title.slice(0, 47) + " - Flash Player Installation";
            var MMdoctitle = document.title;
        
                AC_FL_RunContent(
                        "src", "/player/playerProductInstall",
                        "FlashVars", "MMredirectURL="+MMredirectURL+'&MMplayerType='+MMPlayerType+'&MMdoctitle='+MMdoctitle+"&url=http://towel.radioverve.com/scripts/",
                        "width", "100%",
                        "height", "100%",
                        "align", "middle",
                        "id", "RadioVeRVePlayer",
                        "quality", "high",
                        "bgcolor", "${bgcolor}",
                        "name", "${application}",
                        "allowScriptAccess","sameDomain",
                        "type", "application/x-shockwave-flash",
                        "pluginspage", "http://www.adobe.com/go/getflashplayer"
                );
        } else if (hasRequestedVersion) {
                // if we've detected an acceptable version
                // embed the Flash Content SWF when all tests are passed
                AC_FL_RunContent(
                                
                                "src", "/player/RadioVeRVePlayer",
                                "width", "100%",
                                "height", "100%",
                                "align", "middle",
                                "id", "RadioVeRVePlayer",
                                "quality", "high",
                                "bgcolor", "#FFFFFF",
                                "name", "RadioVeRVePlayer",
                                "allowScriptAccess","sameDomain",
                                "FlashVars","url=http://towel.radioverve.com/scripts/",
                                "type", "application/x-shockwave-flash",
                                "pluginspage", "http://www.adobe.com/go/getflashplayer"
                );
          } else {  // flash is too old or we can't detect the plugin
            var alternateContent = 'Alternate HTML content should be placed here. '
                + 'This content requires the Adobe Flash Player. '
                + '<a href=http://www.adobe.com/go/getflash/>Get Flash</a>';
            document.write(alternateContent);  // insert non-flash content
          }
        
        </script>
        <noscript>
                <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
                                id="RadioVeRVePlayer" width="100%" height="100%"
                                codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
                                <param name="movie" value="/player/RadioVeRVePlayer.swf" />
                                <param name="quality" value="high" />
                                <param name="bgcolor" value="#FFFFFF" />
                                <param name="allowScriptAccess" value="sameDomain" />
                                <embed src="/player/RadioVeRVePlayer.swf" quality="high" bgcolor="#FFFFFF"
                                        width="100%" height="100%" name="RadioVeRVePlayer" align="middle"
                                        play="true"
                                        loop="false"
                                        quality="high"
                                        allowScriptAccess="sameDomain"
                                        type="application/x-shockwave-flash"
                                        pluginspage="http://www.adobe.com/go/getflashplayer">
                                </embed>
                </object>
        </noscript>
</div>