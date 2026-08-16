

<?php $__env->startSection('content'); ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

    /* ---------- Page Header / Typing Title ---------- */
    .page-header__inner {
        text-align: center;
        padding: 64px 0 40px;
    }
    #typed-title{
        display:inline-block; overflow:hidden; white-space:nowrap;
        border-right:3px solid #FFE986;
        font-family:'Montserrat', sans-serif;
        font-size:60px; line-height:1.1;
        background:linear-gradient(90deg,#FFE986 0%, #C48127 100%);
        -webkit-background-clip:text; -webkit-text-fill-color:transparent;
        animation:blink-caret .75s step-end infinite;
    }
    #typed-title.finished{ border-right:none; animation:none; }
    @keyframes blink-caret{ 0%,100%{border-color:transparent} 50%{border-color:#FFE986} }
    @media (max-width: 767.98px){ #typed-title{ font-size:32px; } }

    /* ---------- Company Card (left) ---------- */
    .team-details__left{
        background:linear-gradient(180deg,#f4f2ee 0%, #f4f2ee 25%, #cc252e 75%, #cc252e 100%);
        border-radius:16px; padding:30px; text-align:center; color:#fff;
        box-shadow:0 8px 20px rgba(0,0,0,.3);
    }
    .avatar-ring{
        margin:0 auto; width:160px; height:160px; border-radius:50%; overflow:hidden;
        border:3px solid #cc252e; box-shadow:0 4px 12px rgba(0,0,0,.4); background:#fff;
    }
    .avatar-ring img{ width:100%; height:100%; object-fit:contain; }

    /* ---------- Gradient Text Accent ---------- */
    .stat-number{
        margin-bottom:10px;
        background:linear-gradient(270deg,#fff,#cc252e,#fff);
        background-size:600% 600%;
        -webkit-background-clip:text; -webkit-text-fill-color:transparent;
        animation:gradientShift 5s ease infinite;
    }
    @keyframes gradientShift{
        0%{background-position:0% 50%}
        50%{background-position:100% 50%}
        100%{background-position:0% 50%}
    }

    /* ---------- Vote Button ---------- */
    #voteBtn{ margin-top:20px; padding:8px 36px !important; font-weight:700; }
    #voteBtn.is-loading{ opacity:.75; cursor:wait; }

    /* ---------- Beautiful Popup / Modal ---------- */
    .vote-popup-overlay{
        position:fixed; inset:0; display:none; align-items:center; justify-content:center;
        background:rgba(0,0,0,.74); z-index:9999;
        backdrop-filter:blur(2px);
    }
    .vote-popup-overlay.show{ display:flex; }
    .vote-popup{
        width:min(520px,92vw); background:#0f0f10; color:#fff;
        border:1px solid rgba(255,255,255,.06);
        box-shadow:0 24px 60px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.05);
        border-radius:18px; padding:28px 24px 22px; position:relative;
        transform:translateY(12px) scale(.98); opacity:0;
        animation:popupIn .28s ease forwards;
    }
    @keyframes popupIn{
        to{ transform:translateY(0) scale(1); opacity:1; }
    }
    .vote-popup .close-x{
        position:absolute; top:10px; right:12px; border:none; background:transparent;
        color:#aaa; font-size:22px; line-height:1; cursor:pointer;
        padding:6px; border-radius:8px;
    }
    .vote-popup .close-x:hover{ color:#fff; background:rgba(255,255,255,.06); }
    .vote-head{
        display:flex; align-items:center; gap:14px; margin-bottom:8px;
    }
    .vote-icon{
        width:42px; height:42px; display:grid; place-items:center; border-radius:12px;
        background:linear-gradient(135deg,#1a1a1b,#2a2a2c);
        box-shadow:inset 0 0 0 1px rgba(255,255,255,.06);
    }
    .vote-icon svg{ width:24px; height:24px; }
    .vote-icon.success{ background:linear-gradient(135deg,#093,#1f7); }
    .vote-icon.error{ background:linear-gradient(135deg,#700,#d33); }
    .vote-title{
        font-size:18px; font-weight:800;
        background:linear-gradient(90deg,#fff,#cc252e);
        -webkit-background-clip:text; -webkit-text-fill-color:transparent;
    }
    .vote-message{ color:#ddd; margin:6px 0 14px; line-height:1.6; }

    .vote-actions{
        display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap;
        border-top:1px dashed rgba(255,255,255,.12); padding-top:14px; margin-top:8px;
    }
    .btn-ghost{
        background:transparent; border:1px solid rgba(255,255,255,.18); color:#fff;
        border-radius:10px; padding:10px 14px; font-weight:600;
    }
    .btn-primary-gradient{
background-color: var(--e-global-color-primary, #cc252e);
    color: white;
    border:none; border-radius:10px; padding:10px 16px; font-weight:800;
        box-shadow:0 8px 18px rgba(0,0,0,.25);
    }
    .btn-ghost:hover{ background:rgba(255,255,255,.06); }

    /* Subtle confetti (CSS-only bits) */
    .confetti{
        position:absolute; inset:0; pointer-events:none; overflow:hidden;
    }
    .confetti i{
        position:absolute; width:8px; height:12px; opacity:.9;
        animation:fall 1.6s linear forwards;
    }
    @keyframes fall{
        to{ transform:translateY(140%) rotate(240deg); opacity:0; }
    }
</style>


<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url(<?php echo e(asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg')); ?>);">
    <div class="container"><h3 class="breadcrumbs-custom-title">Voting</h3></div>
</section>


<div class="container">
    <div class="page-header__inner">
    </div>
</div>

<section class="team-details mt-2 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-xl-4 col-lg-5">
                <div class="team-details__left">
                    <div class="avatar-ring">
                        <img src="<?php echo e(asset('public/'.$company->image ?? '')); ?>"
                             alt="<?php echo e($company->name_en); ?>" />
                    </div>

                    <div class="mt-4">
                        <h3 class="stat-number"><?php echo e($company->name_en); ?></h3>
                        <?php if($company->category): ?>
                            <p class="stat-number"><?php echo e($company->category); ?></p>
                        <?php endif; ?>
                      <?php if($company->number_of_followers): ?>
  <span class="followers-bar" style="display:inline-flex;align-items:center;gap:6px;">
    
    <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true" focusable="false" style="display:block">
      <rect x="3" y="3" width="18" height="18" rx="5" ry="5"
            fill="none" stroke="currentColor" stroke-width="1.5"/>
      <circle cx="12" cy="12" r="4.5"
              fill="none" stroke="currentColor" stroke-width="1.5"/>
      <circle cx="17.5" cy="6.5" r="1.25" fill="currentColor"/>
    </svg>

    <p class="stat-number" style="margin:0; font-size:20px;"><?php echo e($company->number_of_followers); ?></p>
  </span>
<?php endif; ?>
         <?php if($company->followers_ticktock): ?>
  <span class="followers-bar" style="display:inline-flex;align-items:center;gap:6px;">
    
    <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true" focusable="false" style="display:block">
                  <path d="M14 3v3.6c1.4 1.2 3.2 2 5 2v2.5c-2.1 0-3.9-.6-5-1.4v4.4A6.5 6.5 0 1 1 9 8.5c.5 0 1 .05 1.5.16v2.6A3.5 3.5 0 1 0 12.5 15V3h1.5z"
                        fill="currentColor"/>
                </svg>

    <p class="stat-number" style="margin:0; font-size:20px;"><?php echo e($company->followers_ticktock); ?></p>
  </span>
<?php endif; ?>
                    </div>

                    <button id="voteBtn"
                            class="btn custom-button-white mt-2"
                            onclick="submitVote(<?php echo e($company->id); ?>)">
                        <?php echo e(__('Vote')); ?>

                    </button>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7">
                <div class="team-details__right">
                    <?php if($company->title_en): ?>
                        <h3><?php echo e($company->title_en); ?></h3>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>


<div id="votePopup" class="vote-popup-overlay" aria-hidden="true">
    <div class="vote-popup" role="dialog" aria-modal="true" aria-labelledby="voteTitle" aria-describedby="voteMessage">
        <button class="close-x" type="button" aria-label="Close" onclick="closeVotePopup()">×</button>

        <div class="vote-head">
            <div id="voteIcon" class="vote-icon" aria-hidden="true">
                
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20Zm1 15h-2v-2h2v2Zm0-4h-2V7h2v6Z"/></svg>
            </div>
            <div class="vote-title" id="voteTitle"><?php echo e(__('Voting Result')); ?></div>
        </div>

        <div id="voteMessage" class="vote-message">…</div>

        <div class="vote-actions">
            <button class="btn-ghost" type="button" onclick="closeVotePopup()"><?php echo e(__('Close')); ?></button>
            <a href="<?php echo e(url()->previous()); ?>" class="btn-primary-gradient"><?php echo e(__('Back')); ?></a>
        </div>

        
        <div id="confetti" class="confetti"></div>
    </div>
</div>

<script>
/* ---------- Typing Title ---------- */
document.addEventListener("DOMContentLoaded", function () {
    const target = document.getElementById("typed-title");
    if (!target) return;
    const text = <?php echo json_encode(__('Voting'), 15, 512) ?>;
    let i = 0;
    (function type() {
        if (i < text.length) {
            target.textContent += text.charAt(i++);
            setTimeout(type, 90);
        } else {
            target.classList.add('finished');
        }
    })();
});

/* ---------- Popup Helpers ---------- */
function openVotePopup() {
    const overlay = document.getElementById('votePopup');
    if (!overlay) return;
    overlay.classList.add('show');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    // focus trap: move focus to close button
    const closeBtn = overlay.querySelector('.close-x');
    if (closeBtn) closeBtn.focus();

    // Escape to close
    document.addEventListener('keydown', escCloseHandler);
}
function closeVotePopup() {
    const overlay = document.getElementById('votePopup');
    if (!overlay) return;
    overlay.classList.remove('show');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';

    document.removeEventListener('keydown', escCloseHandler);
}
function escCloseHandler(e){ if (e.key === 'Escape') closeVotePopup(); }
// click outside to close
document.addEventListener('click', function(e){
    const overlay = document.getElementById('votePopup');
    if (!overlay || overlay.getAttribute('aria-hidden') === 'true') return;
    const modal = overlay.querySelector('.vote-popup');
    if (modal && !modal.contains(e.target) && e.target === overlay) closeVotePopup();
});

/* ---------- Confetti (simple) ---------- */
function throwConfetti() {
    const box = document.getElementById('confetti');
    if (!box) return;
    box.innerHTML = '';
    const colors = ['#FFD166','#06D6A0','#EF476F','#118AB2','#FFC43D'];
    const n = 24;
    for (let i=0;i<n;i++){
        const chip = document.createElement('i');
        const left = Math.random()*100;
        const delay = Math.random()*300;
        const color = colors[Math.floor(Math.random()*colors.length)];
        chip.style.left = left + '%';
        chip.style.top = '-8%';
        chip.style.background = color;
        chip.style.transform = `translateY(0) rotate(${Math.random()*120}deg)`;
        chip.style.animationDelay = delay+'ms';
        box.appendChild(chip);
    }
    // remove after a moment
    setTimeout(()=> box.innerHTML = '', 2200);
}

/* ---------- Vote Action ---------- */
async function submitVote(companyId) {
    const btn = document.getElementById('voteBtn');
    const iconBox = document.getElementById('voteIcon');
    const msgBox  = document.getElementById('voteMessage');
    const titleEl = document.getElementById('voteTitle');

    if (!btn) return;
    btn.classList.add('is-loading');
    btn.disabled = true;
    const originalLabel = btn.textContent.trim();
    btn.textContent = '<?php echo e(__("Voting...")); ?>';

    try {
        const res = await fetch(`<?php echo e(url('/vote')); ?>/${companyId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({})
        });

        const data = await res.json().catch(() => ({}));

        // style icon (success/error)
        iconBox.classList.remove('success','error');
        iconBox.innerHTML = '';
        if (res.ok) {
            iconBox.classList.add('success');
            iconBox.innerHTML = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2Zm-1.1 13.6-3.5-3.5 1.4-1.4 2.1 2.1 4.9-4.9 1.4 1.4-6.3 6.3Z"/></svg>`;
            titleEl.textContent = `<?php echo e(__('Thanks! Your vote was recorded')); ?>`;
            msgBox.textContent = (data && data.message) ? data.message : `<?php echo e(__('Your vote has been counted successfully.')); ?>`;
            btn.textContent = '<?php echo e(__("Voted")); ?>';
            throwConfetti();
        } else {
            iconBox.classList.add('error');
            iconBox.innerHTML = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2Zm1 14h-2v-2h2v2Zm0-4h-2V7h2v5Z"/></svg>`;
            titleEl.textContent = `<?php echo e(__('Oops! Vote failed')); ?>`;
            msgBox.textContent = (data && data.message) ? data.message : `<?php echo e(__('Something went wrong while submitting your vote.')); ?>`;
            btn.textContent = originalLabel;
            btn.disabled = false;
        }

        openVotePopup();
    } catch (err) {
        iconBox.classList.remove('success'); iconBox.classList.add('error');
        iconBox.innerHTML = `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2Zm1 14h-2v-2h2v2Zm0-4h-2V7h2v5Z"/></svg>`;
        document.getElementById('voteTitle').textContent = `<?php echo e(__('Network Error')); ?>`;
        document.getElementById('voteMessage').textContent = `<?php echo e(__('Please check your connection and try again.')); ?>`;
        btn.textContent = originalLabel;
        btn.disabled = false;
        openVotePopup();
    } finally {
        btn.classList.remove('is-loading');
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('web.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/company_details.blade.php ENDPATH**/ ?>