<!-- Container -->
<div class="container asl-p-cont asl-new-bg">
  <div class="row asl-inner-cont">
  <div class="col-md-12">
    <h3  class="alert alert-info head-1">Agile Store Locator Dashboard</h3>
    <div class="alert alert-info" role="alert">
      Support Forum for Bugs or Troubleshooting. <a target="_blank" href="https://wordpress.org/support/plugin/agile-store-locator">Create a Ticket</a> 
    </div>
    <div class="alert alert-info" role="alert">
      Please visit the documentation page to explore all options. <a target="_blank" href="https://agilestorelocator.com?v=1.0.3">Agile Store Locator</a> 
    </div>
    <?php if(!$all_configs['api_key']): ?>
        <h3  class="alert alert-danger" style="font-size: 14px">Google API KEY is missing, the Map Search and Direction will not work without it, Please add Google API KEY. <a href="https://agilestorelocator.com/blog/enable-google-maps-api-agile-store-locator-plugin/?v=1.0.3" target="_blank">How to Add API Key?</a></h3>
      <?php endif; ?>
    <div class="dashboard-area">
      <div class="row">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-3 stats-store">
                <div class="stats">
                    <div class="stats-a"><span class="glyphicon glyphicon-shopping-cart"></span></div>
                    <div class="stats-b"><?php echo $all_stats['stores'] ?><br><span>Stores</span></div>
                </div>
              </div>
              <div class="col-md-3 stats-category">
                <div class="stats">
                    <div class="stats-a"><span class="glyphicon glyphicon-tag"></span></div>
                    <div class="stats-b"><?php echo $all_stats['categories'] ?><br><span>Categories</span></div>
                </div>
              </div>
              <div class="col-md-3 stats-marker">
                <div class="stats">
                    <div class="stats-a"><span class="glyphicon glyphicon-map-marker"></span></div>
                    <div class="stats-b"><?php echo $all_stats['markers'] ?><br><span>Markers</span></div>
                </div>
              </div>
              <div class="col-md-3 stats-searches">
                <div class="stats">
                    <div class="stats-a"><span class="glyphicon glyphicon-search"></span></div>
                    <div class="stats-b"><?php echo $all_stats['searches'] ?><br><span>Searches</span></div>
                </div>
              </div>
            </div>
          </div>
      </div>
      <div class="row"></div>
       <div class="tab-pane fade in active" role="tabpanel" id="amaps-analytics" aria-labelledby="amaps-analytics">
        <div class="row">
          <div class="col-md-12">
            <p class="alert alert-info" style="margin-top: 30px">Analytics with Bar Chart, Top Views and Top Stores is added in <a href="https://agilestorelocator.com/demos" target="_blank">PRO version</a>.</p>
            <img src="<?php echo AGILESTORELOCATOR_URL_PATH.'admin/images/searches.png' ?>" style="max-width:100%;margin-top: 0px">
          </div>
        </div>
      </div>
    </div>

    <div class="dump-message asl-dumper"></div>
  </div>  
  </div>
</div>
<!-- asl-cont end-->








<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
	url: '<?php echo AGILESTORELOCATOR_URL_PATH ?>'
};

var ASL_upload = '<?php echo AGILESTORELOCATOR_PLUGIN_PATH ?>';
//asl_engine.pages.dashboard();
</script>