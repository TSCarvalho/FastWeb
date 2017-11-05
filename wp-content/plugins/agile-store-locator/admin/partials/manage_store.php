<!-- Container -->
<style type="text/css">
  #tbl_stores_wrapper {position: relative;}
  #tbl_stores_length {position: absolute; top: -36px; left: 11px;}
</style>
<div class="asl-p-cont">
  <div>
	<h3 class="alert alert-info head-1">Manage Stores</h3>
  <div class="row">
    <div class="col-md-3 pull-right">
      <button type="button" id="asl-delete-stores" class="btn btn-primary btn-danger">Delete All Stores</button>
    </div>
  </div>
	<table id="tbl_stores" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th align="center"><input type="text" data-id="id"  placeholder="Search ID"  /></th>
          <th align="center"><input type="text" data-id="title"  placeholder="Search Title"  /></th>
          <th align="center"><input type="text" data-id="description"  placeholder="Search Description"  /></th>
          <th align="center"><input type="text" data-id="lat"  placeholder="Lat"  /></th>
          <th align="center"><input type="text" data-id="lng"  placeholder="Lng"  /></th>
          <th align="center"><input type="text" data-id="street"  placeholder="Search Street"  /></th>
          <th  align="center"><input type="text" data-id="state"  placeholder="Search State"  /></th>
          <th  align="center"><input type="text" data-id="city"  placeholder="Search City"  /></th>
          <th  align="center"><input type="text" data-id="phone"  placeholder="Search Phone"  /></th>
          <th  align="center"><input type="text" data-id="email"  placeholder="Search Email"  /></th>
          <th  align="center"><input type="text" data-id="website"  placeholder="Search URL"  /></th>
          <th  align="center"><input type="text" data-id="postal_code"  placeholder="Search Zip"  /></th>
          <th  align="center"><input type="text" data-id="is_disabled"  placeholder="Disabled"  /></th>
          <th  align="center"><input type="text" data-id="category" disabled="disabled" style="opacity:0"  placeholder="Categories"  /></th>
          <th  align="center"><input type="text" data-id="marker_id"  placeholder="Marker ID"  /></th>
          <th  align="center"><input type="text" data-id="logo_id"  placeholder="Logo ID" /></th>
          <th  align="center"><input type="text" data-id="start_time"  placeholder="Start Time"  /></th>
          <th  align="center"><input type="text" data-id="end_time"  placeholder="End Time"  /></th>
          <th  align="center"><input type="text" data-id="days"  placeholder="Created On" style="visibility: hidden"  /></th>
          <th  align="center"><input type="text" data-id="created_on"  placeholder="Created On"  /></th>
          <th  align="center"><Button type="button" class="btn btn-default" id="Search_Data">Search</Button></th>
        </tr>
        <tr>
          <th align="center">Store ID</th>
          <th align="center">Title</th>
          <th align="center">Description</th>
          <th align="center">lat</th>
          <th align="center">lng</th>
          <th align="center">Street</th>
          <th align="center">State</th>
          <th align="center">City</th>
          <th align="center">Phone</th>
          <th align="center">Email</th>
          <th align="center">URL</th>
          <th align="center">Postal Code</th>
          <th align="center">Disabled</th>
          <th align="center">Categories</th>
          <th align="center">Marker ID</th>
          <th align="center">Logo ID</th>
          <th align="center">Start Time</th>
          <th align="center">End Time</th>
          <th align="center">Days</th>
          <th align="center">Created On</th>
          <th align="center">Action&nbsp;</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
	<div class="dump-message asl-dumper"></div>
  </div>
</div>

<div class="smodal fade asl-p-cont" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="smodal-dialog">
        <div class="smodal-content">
            <div class="smodal-header">
                <h4>Delete Store</h4>
            </div>
            <div class="smodal-body">
                Are you sure you want to delete selected store?
            </div>
            <div class="smodal-footer">
                <button type="button" class="btn btn-default" data-dismiss="smodal">Cancel</button>
                <a class="btn btn-danger btn-ok" id="btn-delete-store">Delete</a>
            </div>
        </div>
    </div>
</div>



<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
	url: '<?php echo AGILESTORELOCATOR_URL_PATH ?>'
};
asl_engine.pages.manage_stores();
</script>
