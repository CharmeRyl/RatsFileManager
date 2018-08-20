<!-- Upload progress -->
<div class="modal fade" id="uploadProgress" tabindex="-1" role="dialog" aria-labelledby="uploadProgressTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadProgressTitle">Uploading File</h5>
            </div>
            <div class="modal-body">
                <p id="uploadFileName" class="my-1"></p>
                <div class="progress">
                    <div id= "uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create folder -->
<div class="modal fade" id="createFolder" tabindex="-1" role="dialog" aria-labelledby="createFolderTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createFolderTitle">New Folder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form onsubmit="return false;">
                    <div class="form-group">
                        <label for="folder-name" class="col-form-label">Folder name:</label>
                        <input type="text" class="form-control" id="folder-name" name="name">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="create_dir($('#folder-name')[0].value)">Create</button>
            </div>
        </div>
    </div>
</div>

<!-- Rename item -->
<div class="modal fade" id="renameItem" tabindex="-1" role="dialog" aria-labelledby="renameItemTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renameItemTitle">Rename</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form onsubmit="return false;">
                    <div class="form-group">
                        <label for="rename-name-new" class="col-form-label">Rename to:</label>
                        <input type="text" class="form-control" id="rename-name-new">
                    </div>
                    <input type="hidden" id="rename-name">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="rename_file($('#rename-name')[0].value, $('#rename-name-new')[0].value)">Apply</button>
            </div>
        </div>
    </div>
</div>