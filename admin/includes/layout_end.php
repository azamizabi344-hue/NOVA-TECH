<?php
/**
 * NOVA TECH - Admin Layout Ending
 * 
 * Use this at the bottom of every admin page.
 * It closes the layout divs and adds the admin JavaScript.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
?>
    </div><!-- /.admin__content -->
</main>
</div><!-- /.admin-layout -->

<!-- Toast container for notifications -->
<div id="toastContainer" class="toast-container"></div>

<script src="<?= base_url('js/main.js') ?>"></script>
<script src="<?= base_url('admin/js/admin.js') ?>"></script>
</body>
</html>