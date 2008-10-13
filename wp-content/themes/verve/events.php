<?php
/*                                                                                                                                                                                 
Template Name: Events                                                                                                                                                              
*/
?>

<?php get_header(); ?>
<!-- The weird alignment is to show open divs in the header-->
            <div id="content">
                    <div id="plainContentMiddle">
                        <div id="pagecontent">
                            <h1>Find Events</h1>
                            <form id="eventSearchForm" class="clearit" method="get" action="http://test.radioverve.com/new/events/">   
                               <div>
                                   <label for="s">Enter Artist, festival or venue</label>
                                   <input id="s" type="text" name="searchstring" />
                                   <p id="pastupcoming">
                                       Search:
                                       <input id="upcoming" type="radio" checked="checked" value="1" name="upcoming" />
                                       <label for="upcoming">
                                           <small>upcoming</small>
                                       </label>
                                       <input id="past" type="radio" value="0" name="past"/>
                                       <label for="past">
                                           <small>past</small>
                                       </label>
                                   </p>
                               </div>
                               <div>
                                    <label for="findloc">
                                        Where
                                        <small>(e.g. Mumbai, Chennai or Delhi)</small>
                                    </label>
                                    <input id="findloc" type="text" value="Bangalore" name="findloc"/>
                                    <p id="within">
                                    </p>
                                </div>
                                <div>
                                    <input id="submit" type="submit" value="Search"/>
                                </div>
                            </form>
                       </div>
                
                        <?
                            if(isset($_GET["searchstring"])){
                                gigpress_upcoming($_GET["searchstring"],$_GET["findloc"],$GET["upcoming"]==1?1:0);
                            }else 
                                gigpress_upcoming();
                        ?>
                    </div>
            </div>
	</div>
    </div>		
</div>
<? get_footer(); ?>