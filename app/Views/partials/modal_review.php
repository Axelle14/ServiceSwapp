<?php use App\Core\Validator; ?>
<div class="modal-overlay" id="modal-review">
  <div class="modal">
    <h2 class="modal-title">Leave a Review</h2>
    <p class="modal-sub">Rate your experience with this swap</p>
    <input type="hidden" id="reviewSwapId">
    <div class="form-group">
      <label>Rating</label>
      <div id="starContainer" style="display:flex;gap:8px;font-size:28px;cursor:pointer;margin-bottom:4px">
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <span class="star" data-val="<?= $i ?>" style="color:var(--light);transition:color .15s">★</span>
        <?php endfor; ?>
      </div>
      <input type="hidden" id="reviewRating" name="rating" value="5">
    </div>
    <div class="form-group">
      <label>Comment</label>
      <textarea id="reviewComment" rows="3" placeholder="How was your experience with this swap?"></textarea>
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost" onclick="closeModal('review')">Cancel</button>
      <button class="btn btn-primary" id="reviewSubmitBtn" onclick="submitReview()">Submit Review</button>
    </div>
  </div>
</div>
