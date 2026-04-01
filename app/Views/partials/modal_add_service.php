<?php
use App\Core\Validator;
use App\Middleware\Auth;
$_svcCategories = ['Design', 'Tech', 'Writing', 'Photography', 'Tutoring', 'Home Services', 'Music', 'Other'];
?>
<?php if (Auth::check()): ?>
<div class="modal-overlay" id="modal-addService">
  <div class="modal">
    <h2 class="modal-title">List a Service</h2>
    <p class="modal-sub">Offer your skills and earn credits</p>
    <form onsubmit="submitService(event)" id="serviceForm">
      <div class="form-group">
        <label>Service title</label>
        <input type="text" name="title" placeholder="e.g. Responsive Landing Page Design" required>
      </div>
      <div class="form-group">
        <label>Category</label>
        <select name="category" required>
          <?php foreach ($_svcCategories as $cat): ?>
            <option value="<?= Validator::e($cat) ?>"><?= Validator::e($cat) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" rows="3" placeholder="Describe exactly what you'll provide…" required></textarea>
      </div>
      <div class="form-group">
        <label>Credit value (1–500)</label>
        <input type="number" name="credits" min="1" max="500" placeholder="e.g. 25" required>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" onclick="closeModal('addService')">Cancel</button>
        <button type="submit" class="btn btn-primary">Publish Listing</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>
