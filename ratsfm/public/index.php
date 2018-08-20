<nav class="navbar navbar-expand-lg navbar-dark bg-secondary shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand" href="javascript:void(0);">
            <img src="<?php echo $_STATIC_URI; ?>/img/brand-punchout.svg" width="30" height="30" class="d-inline-block align-top" alt="">
            <?php echo $_SITE_NAME; ?>
        </a>
        <div class="collapse navbar-collapse" id="user-navbar">
            <div class="navbar-nav ml-auto">
                <button class="btn btn-outline-light btn-upload my-2 mr-2 my-sm-0"><span>Upload</span><input type="file" id="upload-btn" onchange="upload_file(this);"></button>
                <button class="btn btn-outline-light my-2 mr-2 my-sm-0" type="button" data-toggle="modal" data-target="#createFolder">New Folder</button>
                <?php if(isset($username)): ?>
                <div class="dropdown">
                    <a href="javascript:void(0);" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="rounded-circle" src="<?php echo $_STATIC_URI; ?>/img/avatar.svg" alt="" width="36" height="36">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                        <h6 class="dropdown-header"><?php echo $username; ?></h6>
                        <a class="dropdown-item" href="javascript:void(0);" onclick="user_logout()">Sign out</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main role="main" class="container">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <?php for($i = 0; $i < count($breadcrumb['keys']); $i++): ?>
            <?php if($i < count($breadcrumb['keys']) - 1): ?>
            <li class="breadcrumb-item"><a href="<?php echo $breadcrumb['urls'][$i]; ?>"><?php echo $breadcrumb['keys'][$i]; ?></a></li>
            <?php else: ?>
            <li class="breadcrumb-item active"><?php echo $breadcrumb['keys'][$i]; ?></li>
            <?php endif; ?>
            <?php endfor; ?>
        </ol>
    </nav>

    <table class="table table-borderless table-hover bg-white">
        <thead class="thead-light">
        <tr>
            <th class="table-file-sel" scope="col"><input type="checkbox" title="Invert Select" onclick="checkbox_toggle()"></th>
            <th class="table-file-name" scope="col">Name</th>
            <th class="table-file-size" scope="col">Size</th>
            <th class="table-file-time" scope="col">Time</th>
            <th class="table-file-ops" scope="col">Ops</th>
        </tr>
        </thead>
        <tbody>
        <?php if(count($items) == 0): ?>
            <tr><td class="text-center" colspan="5">Empty Folder</td></tr>
        <?php endif; ?>
        <?php foreach($items as $item): ?>
            <tr>
                <td class="table-file-sel"><label><input type="checkbox" name="file[]" value="<?php echo $item['name']; ?>"></label></td>
                <td class="table-file-name" onclick="this.children[0].click();">
                    <?php if($item['type'] == 'folder'): ?>
                    <a href="<?php echo $item['url']['link']; ?>"><i class="fa fa-folder-o" aria-hidden="true"></i><?php echo ' ' . $item['name']; ?></a>
                    <?php else: ?>
                    <a href="<?php echo $item['url']['download']; ?>"><i class="fa fa-file-o" aria-hidden="true"></i><?php echo ' ' . $item['name']; ?></a>
                    <?php endif; ?>
                </td>
                <td class="table-file-size"><?php echo $item['size']; ?></td>
                <td class="table-file-time"><?php echo $item['mtime']; ?></td>
                <td class="table-file-ops">
                    <a class="btn btn-danger btn-sm ladda-button" href="javascript:void(0);" onclick="delete_file('<?php echo $item['name']; ?>')" data-style="zoom-out"><span class="ladda-label">Delete</span></a>
                    <a class="btn btn-info btn-sm" href="javascript:void(0);" onclick="show_modal('#renameItem', {'#rename-name-new': '<?php echo $item['name']; ?>', '#rename-name': '<?php echo $item['name']; ?>'});">Rename</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>


</main><!-- /.container -->

<footer class="my-5 pt-5 text-muted text-center text-small">
    <p class="mb-1">© 2017-2018 <?php echo $_SITE_NAME; ?></p>
</footer>

<?php include('include/modals.php'); ?>