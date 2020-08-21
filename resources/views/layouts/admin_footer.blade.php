        <!-- Sticky Footer -->
        <footer class="sticky-footer">
          <div class="container my-auto">
            <div class="copyright text-center my-auto">
              <span>Copyright &copy www.infidia.in <?php echo date('Y');?></span>
            </div>
          </div>
        </footer>

      </div>
      <!-- /.content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
    </a>
    
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <a class="btn btn-primary" href="login.html">Logout</a>
          </div>
        </div>
      </div>
    </div>
    <input type="hidden" value="{{url('/')}}" id="baseUrl">
    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Page level plugin JavaScript-->
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.js') }}"></script>
    <script>
      $('#dataTable').dataTable( {
        "pageLength": 50
      });
    </script>
    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin.min.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>

    <!-- Demo scripts for this page-->
    <script src="{{ asset('js/demo/datatables-demo.js') }}"></script>
    <script src="{{ asset('js/demo/chart-area-demo.js') }}"></script>
    <script type="text/javascript">
       $(document).ready(function() {
          $("#subscription_price").keydown(function(event) {
            
              // Allow only backspace and delete
              if ( event.keyCode == 46 || event.keyCode == 8 ||  event.keyCode == 9) {
                  // let it happen, don't do anything
              }
              else {
                  // Ensure that it is a number and stop the keypress
                  if (event.keyCode < 48 || event.keyCode > 57 ) {
                      event.preventDefault(); 
                  }   
              }
          });
      });
    </script>

    <!-- chosen -->
    <script src="{{ asset('js/chosen/chosen.jquery.js') }}" type="text/javascript"></script>
    <script>
      $(".chosen-select").chosen();
    </script>

	<!-- Ckeditor scripts-->
	<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
	<script type="text/javascript">
		 CKEDITOR.replace( 'description',
		 {
		  customConfig : 'config.js',
		  toolbar : 'simple'
		  });
   
	</script>	
  <script type="text/javascript">
    count = 0;  
    $(document).on('click', '#attr_add_options', function(e) {
      var baseUrl   = $('#baseUrl').val();
      var divlength = $('.HtmlAttr').length;
      var attr_id   = $("#attribute_set_id").val();
      //alert(attr_id);
      $.ajax({
          type: 'get',
          dataType: 'html',
          url: baseUrl+'/product/getHtml/'+divlength+'/'+attr_id,
          success: function(extraAttr) { 
            $('#attr_options_div:last').before('<div class="">'+extraAttr+'<input type="button" class="btn btn-danger attr_remove-button-new" value="Remove" ></div>');
          }
      });    
    });
    $(document).on('click', '.attr_remove-button-new', function(e) {
      $(this).parent().remove();
    });
  </script>
  </body>

</html>