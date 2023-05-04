<div class="col-md-4">
    <div class="card account-left">
        <div class="user-profile-header">
            <h5 class="mb-1 text-secondary">
                <strong>Hi </strong> <?php echo $fn->session('user', 'first_name'); ?></h5>
            <p> <?php echo $fn->session('user', 'mobile_no'); ?></p>
        </div>
        <div class="list-group">
            <a href="<?php echo $fn->permalink('account'); ?>"
               class="list-group-item list-group-item-action<?php echo $fn->get('page_url') == 'account' ? ' active' : ''; ?>">
                <i aria-hidden="true" class="mdi mdi-account-outline"></i> My Profile</a>
            <a href="<?php echo $fn->permalink('account/addresses'); ?>" class="list-group-item list-group-item-action<?php echo $fn->get('page_url') == 'addresses' ? ' active' : ''; ?>"><i
                        aria-hidden="true" class="mdi mdi-map-marker-circle"></i> My Address</a>
            <a href="<?php echo $fn->permalink('account/orders'); ?>" class="list-group-item list-group-item-action<?php echo $fn->get('page_url') == 'orders' ? ' active' : ''; ?>"><i
                        aria-hidden="true" class="mdi mdi-format-list-bulleted"></i> Order List</a>
            <a href="<?php echo $fn->permalink('logout'); ?>" class="list-group-item list-group-item-action<?php echo $fn->get('page_url') == 'account' ? ' logout' : ''; ?>"><i
                        aria-hidden="true" class="mdi mdi-lock"></i> Logout</a>
        </div>
    </div>
</div>