{{Admin::disablePjax()}}


    <div id="container">
        <a class="btn btn-default btn-lg pull-left" id="pickfiles" href="#" >
            <i class="glyphicon glyphicon-plus"></i>
            <span>选择文件</span>
        </a>
    </div>

    <div style="display:none;margin-top: 10px;" id="success" class="col-md-12">
        <div class="alert-success" style="line-height: 40px">
              队列全部文件处理完毕
        </div>
    </div>
    <div class="col-md-12 ">
        <table class="table table-striped table-hover text-left"   style="margin-top:40px;display:none">
            <thead>
              <tr>
                <th class="col-md-4">Filename</th>
                <th class="col-md-2">Size</th>
                <th class="col-md-6">Detail</th>
              </tr>
            </thead>
            <tbody id="fsUploadProgress">
            </tbody>
        </table>
    </div>




<script type="text/javascript" src="/qiniu/demo/scripts/jquery.min.js"></script>
<script type="text/javascript" src="/qiniu/demo/scripts/bootstrap.min.js"></script>
{{--<script type="text/javascript" src="bower_components/plupload/js/i18n/zh_CN.js"></script>--}}
<script type="text/javascript" src="/qiniu/demo/scripts/ui.js"></script>
<script type="text/javascript" src="/qiniu/dist/qiniu.min.js"></script>
<!-- <script type="text/javascript" src="scripts/highlight.js"></script> -->
<script type="text/javascript" src="/qiniu/demo/scripts/main.js"></script>


