{{-- See snipeit_modals.js for what powers this --}}
<script src="/js/pGenerator.jquery.js"></script>


<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{trans('admin/inventories/table.inventory_result_edit')}}</h4>
        </div>
            <div class="modal-body">
                <form action="{{ route('api.inventoryresults.store') }}" onsubmit="return false">
                    <div class="alert alert-danger" id="modal_error_msg" style="display:none">
                    </div>
            
                    <div class="dynamic-form-row">
                        @include ('partials.forms.edit.asset-select', ['translated_name' => trans('admin/hardware/form.name'), 'fieldname' => 'asset_id' ,'required' => 'true'])
                    </div>
                    <div class="dynamic-form-row">
                        @include ('partials.forms.edit.status-inventory-result', ['translated_name' => trans('admin/hardware/form.status') , 'fieldname' => 'status_id'])
                    </div>
                    <div class="dynamic-form-row">
                        <div class="col-md-4 col-xs-12">
                            <label for="modal-last_name">{{trans('admin/inventories/table.unrecognized_flag')}}</label></div>
              
                        <div class="col-md-6 col-xs-12">
                            <div id="Unknown"  class="col-md-6 btn {{isset($familiar) ? ($familiar == 0 ? 'btn-danger' : 'btn-default' ) :'btn-danger'}}">{{trans('admin/inventories/table.unknown')}} <input type="radio" class="custom-control-input" id="Unknown_radio" name="checked" hidden="true" value='0' {{isset($familiar) ? ($familiar == 0 ? 'checked' : '' ) :''}}></div>
                            <div id="Familiar" class="col-md-6 btn {{isset($familiar) ? ($familiar == 1 ? 'btn-danger' : 'btn-default' ) :'btn-default'}}">{{trans('admin/inventories/table.familiar')}}<input type="radio" class="custom-control-input" id="Farmilar_radio" name="checked" hidden="true" value='1' {{isset($familiar) ? ($familiar == 1 ? 'checked' : '' ) :''}}></div>
                        </div>
                    </div>
                    <input type="text" class="custom-control-input" id="inventoryid" name="inventory_id" value="{{isset($inventory_id) ? $inventory_id :''}}" hidden>
       


              
                </form>
            </div> 
          
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('button.cancel') }}</button>
            <button type="button" class="btn btn-primary" id="modal-save">{{ trans('general.save') }}</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->


<script nonce="{{ csrf_token() }}">
    $table = $('#inventoryresultTable')
    $("#createModal").on('hidden.bs.modal', function(){
        $table.bootstrapTable('refresh');
    });
    
     $('#assigned_asset_select').change(function () {

      var elem = $('#status_select')
      var asset_id=$('#assigned_asset_select').val();
      
      var js_inventory_id = '{{$inventory_id}}';

      if (js_inventory_id == "") {
        js_inventory_id = $('#inventory_select').val();
      }
      
      $.ajax({
      url:"{{ route('api.inventoryresults.checkasset') }}", // đường dẫn khi gửi dữ liệu đi 'search' là tên route mình đặt bạn mở route lên xem là hiểu nó là cái j.
      method:"POST", 
      headers: {
            "X-Requested-With": 'XMLHttpRequest',
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
        },
      data:{
        asset_id:asset_id,
        inventory_id:js_inventory_id,
        },
      success:function(data){ //dữ liệu nhận về
        elem.append($('<option>', { 
            value: data.status_id,
            text : data.status_name,
            selected : "selected"
        }));
        elem.trigger("change");
        if(data.Recongnized == 1)
        {
            $("#Farmilar_radio").prop("checked", true);
            $("#Unknown").attr('class', 'col-md-6 btn btn-default');
            $("#Familiar").attr('class', 'col-md-6 btn btn-danger');
        }
        else
        {
            $("#Unknown_radio").prop("checked", true);
            $("#Unknown").attr('class', 'col-md-6 btn btn-danger');
            $("#Familiar").attr('class', 'col-md-6 btn btn-default');
        }
       
        $("#inventoryid").val(js_inventory_id);
        
     }
   });
    
    });  
</script>
