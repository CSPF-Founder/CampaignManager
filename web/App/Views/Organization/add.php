
<script>
    $(document).ready(function () {
        $(document).on("submit", "#add-entry-form", function(event){
            event.preventDefault();
            $.post("add", $("#add-entry-form").serialize(), function(response,status) {
                    if(response.redirect) { redirectToLogin(response.redirect); }
                    else if(response.success) {
                        showSuccess(response.success)
                        $("#add-entry-form")[0].reset();
                    }
                    else{
                        showError(response.error);
                    }
                },
                "json").fail(function(response) {
                showError('Error occurred');
            });
        });
    });
</script>
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Organization <small>add</small></h3>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Add Organization</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br />
                        <form id="add-entry-form" method="POST" data-parsley-validate autocomplete="off" class="form-horizontal form-label-left">

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" id="name" placeholder="Name" name="name" required="required" class="form-control col-md-7 col-xs-12">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Max Constituency <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="number" id="max_constituency" placeholder="Max Constituency" value="1" min="0" name="max_constituency" required="required" class="form-control col-md-7 col-xs-12">
                                </div>
                            </div>

                            <div class="ln_solid"></div>
                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <input type="submit"  class="btn btn-success" value="Add"/>
                                </div>
                            </div>

                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->