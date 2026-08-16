


<?php $__env->startSection('title', __('Lead Details')); ?>

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<style>
  :root{
    --card-radius:18px;
    --card-border:#e9edf3;
    --card-shadow:0 10px 28px rgba(15,23,42,.06);
    --ink:#111827;
    --muted:#6b7280;
    --primary:#111827;
    --ok:#16a34a;
    --warn:#f59e0b;
    --danger:#dc2626;

    /* Unified sizing */
    --btn-h:40px;
    --btn-r:12px;
    --ic:18px;
  }

  /* --- Layout --- */
  .container-xxl{ max-width: 1280px; }
  .page-hero{ background:#fff; border:1px solid var(--card-border); border-radius:24px; padding:22px; box-shadow: var(--card-shadow); }
  .subtle{ color: var(--muted); }
  .back-link{ text-decoration:none; color: var(--muted); }
  .back-link:hover{ color: var(--ink); }

  /* --- Cards --- */
  .card-white{ background:#fff; border:1px solid var(--card-border); border-radius: var(--card-radius); box-shadow: var(--card-shadow); }
  .card-white .card-head{ padding:14px 16px; border-bottom:1px dashed var(--card-border); }
  .card-white .card-body{ padding:16px; }

  /* --- Avatar / Chip --- */
  .avatar{
    width:48px;height:48px;border-radius:50%;
    background:#f3f6fa;border:1px solid #eef2f7;
    font-weight:700;display:inline-flex;align-items:center;justify-content:center;
  }
  .lead-chip{ border-radius:999px;padding:6px 14px;font-size:.82rem;font-weight:700; border:1px solid #e5e7eb; height: var(--btn-h); display:inline-flex; align-items:center; gap:8px; }
  .chip-new{ background:#eef2f7;color:#0f172a; }
  .chip-follow{ background:#e6fbff;color:#164e63; }
  .chip-accept{ background:#e9fbf0;color:#166534; }
  .chip-reject{ background:#ffe9e9;color:#991b1b; }
  .chip-lost{ background:#f1f3f5;color:#374151; }

  /* --- Unified Buttons & Icons --- */
  .btn-u{
    height: var(--btn-h);
    border-radius: var(--btn-r);
    padding: 0 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-weight: 600;
    line-height: 1;
  }
  .btn-soft{
    background:#fff; border:1px solid var(--card-border);
  }
  .btn-soft:hover{ box-shadow: var(--card-shadow); }
  .btn-icon{
    width: var(--btn-h);
    height: var(--btn-h);
    padding: 0;
    border-radius: var(--btn-r);
    background:#fff; border:1px solid var(--card-border);
    display:inline-flex; align-items:center; justify-content:center;
  }
  .btn-group .dropdown-toggle.btn-u{ border:1px solid var(--card-border); }
  .btn{ height: var(--btn-h); display:inline-flex; align-items:center; gap:8px; border-radius: var(--btn-r); }

  .ic{ font-size: var(--ic); }
  .fa-fw{ width: 1.25em; text-align: center; }

  /* --- Sidebar --- */
  .sticky-sidebar{ position: sticky; top: 90px; }

  /* --- Tabs --- */
  .nav-pills .nav-link{
    border:1px solid var(--card-border); border-radius:var(--btn-r);
    color: var(--ink); background:#fff; font-weight:600;
    height: var(--btn-h); display:flex; align-items:center; gap:8px;
  }
  .nav-pills .nav-link.active{ background: var(--primary); color:#fff; border-color: var(--primary); }

  /* --- Timeline --- */
  .timeline{ position:relative;padding-inline-start:22px; }
  .timeline:before{ content:""; position:absolute; inset-inline-start:8px; top:0; bottom:0; width:2px; background:#eef2f7; }
  .tl-item{ position:relative; margin-bottom:16px; }
  .tl-item:before{ content:""; position:absolute; inset-inline-start:-2px; top:6px; width:10px; height:10px; background:#111827; border-radius:50%; }
  .note{ color: var(--muted); font-size:.9rem; }

  /* --- Utilities --- */
  .kv .k{ font-size:.78rem; color: var(--muted); }
  .kv .v{ font-weight:600; }
  .danger-text{ color: var(--danger); font-weight:700; }
  .ok-text{ color: var(--ok); font-weight:700; }
  .truncate-2{ display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }

  /* --- QR --- */
  .qr-img{
    max-width: 320px;
    border-radius: 12px;
    border: 1px solid var(--card-border);
    padding: 6px;
    background:#fff;
  }

  @media (max-width: 991px) {
    .sticky-sidebar{ position: static; }
  }
</style>

<?php
  /** Locked statuses */
  $locked = in_array($lead->status, ['accept','reject']);
  $chipClass = [
    'new'=>'chip-new','follow_up'=>'chip-follow','accept'=>'chip-accept','reject'=>'chip-reject','lost'=>'chip-lost'
  ][$lead->status] ?? 'chip-new';

  /** Resolve QR image path from qrcodes table (support qecode_id fallback) */
  $leadQrId = $lead->qrcode_id ?? $lead->qecode_id ?? null;
  $qrFile = null;
  try {
      if ($leadQrId) {
          $qrFile = \Illuminate\Support\Facades\DB::table('qrcodes')->where('id', $leadQrId)->value('qrcode'); // filename.png
      }
  } catch (\Throwable $e) {
      $qrFile = null;
  }
  $qrImgUrl = $qrFile ? asset('public/qrcodes/'.$qrFile) : null;
?>

<div class="container-xxl py-3">

  
  <div class="page-hero mb-3">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div class="d-flex align-items-center gap-3">
        <a href="<?php echo e(route('dashboard.leads.index')); ?>" class="back-link btn-u btn-soft" style="padding-inline:12px">
          <i class="fa fa-arrow-left ic fa-fw"></i> Back
        </a>
        <div class="avatar"><?php echo e(mb_substr($lead->name,0,1)); ?></div>
        <div>
          <h4 class="mb-1"><?php echo e($lead->name); ?></h4>
          <div class="subtle small">
            <i class="fa fa-envelope ic fa-fw me-1"></i>
            <a href="mailto:<?php echo e($lead->email); ?>"><?php echo e($lead->email ?: '—'); ?></a>
            <span class="mx-2">•</span>
            <i class="fa fa-phone ic fa-fw me-1"></i>
            <a href="tel:<?php echo e($lead->phone); ?>"><?php echo e($lead->phone ?: '—'); ?></a>
          </div>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">
        <span class="lead-chip <?php echo e($chipClass); ?>">
          <i class="fa fa-tag ic fa-fw"></i> <?php echo e(ucfirst(str_replace('_',' ',$lead->status))); ?>

        </span>

        
        <?php if(!$locked): ?>
          <div class="btn-group">
            <button class="btn-u btn-soft dropdown-toggle" data-bs-toggle="dropdown">
              <i class="fa fa-gear ic fa-fw"></i> Status
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <?php $__currentLoopData = ['new','follow_up','accept','reject','lost']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                  <form action="<?php echo e(route('dashboard.leads.status',$lead)); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="status" value="<?php echo e($st); ?>">
                    <button class="dropdown-item" type="submit">
                      <i class="fa fa-tag ic fa-fw me-1"></i><?php echo e(ucfirst(str_replace('_',' ',$st))); ?>

                    </button>
                  </form>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
          <form action="<?php echo e(route('dashboard.leads.accept',$lead)); ?>" method="post" class="ms-1">
            <?php echo csrf_field(); ?>
            <button class="btn-u btn-success"><i class="fa fa-check ic fa-fw"></i> Accept</button>
          </form>
        <?php else: ?>
          <button class="btn-u btn-soft" data-bs-toggle="modal" data-bs-target="#statusLockedModal" title="Status is final">
            <i class="fa fa-lock ic fa-fw"></i> Status is final
          </button>
          <?php if($lead->status === 'accept'): ?>
            <?php if($qrImgUrl): ?>
              <a href="<?php echo e($qrImgUrl); ?>" download class="btn-u btn-dark">
                <i class="fa fa-download ic fa-fw"></i> Download QR
              </a>
            <?php else: ?>
              <span class="btn-u btn-soft" title="QR not found"><i class="fa fa-qrcode ic fa-fw"></i> QR not found</span>
            <?php endif; ?>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  
  <div class="row g-3">

    
    <div class="col-lg-8">
      
      <div class="row g-3 mb-1">
        <div class="col-6 col-md-3">
          <div class="card-white">
            <div class="card-body">
              <div class="k d-flex align-items-center subtle"><i class="fa fa-comments ic fa-fw me-2"></i>Comments</div>
              <div class="v fs-5"><?php echo e($lead->comments->count()); ?></div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card-white">
            <div class="card-body">
              <div class="k d-flex align-items-center subtle"><i class="fa fa-phone ic fa-fw me-2"></i>Calls</div>
              <div class="v fs-5"><?php echo e($lead->callLogs->count()); ?></div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card-white">
            <div class="card-body">
              <div class="k subtle"><i class="fa fa-clock ic fa-fw me-2"></i>Last Activity</div>
              <div class="v">
                <?php if(isset($lastActionType) && $lastActionType): ?>
                  <?php echo e($lastActionType === 'comment' ? 'Comment' : 'Call'); ?>

                  <span class="note">• <?php echo e(\Carbon\Carbon::parse($lastActionAt)->diffForHumans()); ?></span>
                <?php else: ?>
                  —
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <?php
            $overdue = $lead->next_call_at && \Carbon\Carbon::parse($lead->next_call_at)->lt(now()) && in_array($lead->status,['new','follow_up']);
          ?>
          <div class="card-white">
            <div class="card-body">
              <div class="k subtle"><i class="fa fa-calendar-check ic fa-fw me-2"></i>Next Follow-up</div>
              <div class="v <?php echo e($overdue ? 'danger-text' : ''); ?>">
                <?php echo e($lead->next_call_at ? \Carbon\Carbon::parse($lead->next_call_at)->format('Y-m-d H:i') : '—'); ?>

              </div>
            </div>
          </div>
        </div>
      </div>

      
      <ul class="nav nav-pills mb-2" id="leadTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="comments-tab" data-bs-toggle="pill" data-bs-target="#comments-pane" type="button" role="tab">
            <i class="fa fa-comments ic fa-fw"></i> Comments <span class="badge bg-secondary ms-1"><?php echo e($lead->comments->count()); ?></span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="calls-tab" data-bs-toggle="pill" data-bs-target="#calls-pane" type="button" role="tab">
            <i class="fa fa-phone ic fa-fw"></i> Calls <span class="badge bg-secondary ms-1"><?php echo e($lead->callLogs->count()); ?></span>
          </button>
        </li>
      </ul>

      <div class="tab-content">
        
        <div class="tab-pane fade show active" id="comments-pane" role="tabpanel" aria-labelledby="comments-tab">
          <div class="card-white mb-3">
            <div class="card-head d-flex align-items-center justify-content-between">
              <div class="fw-semibold"><i class="fa fa-plus ic fa-fw me-2"></i>Add Comment</div>
            </div>
            <div class="card-body">
              <form method="post" action="<?php echo e(route('dashboard.leads.comments.store',$lead)); ?>">
                <?php echo csrf_field(); ?>
                <textarea name="comment" rows="3" class="form-control mb-2" placeholder="Write a note or contact outcome..." required></textarea>
                <div class="row g-2 align-items-end">
                  <div class="col-md-8">
                    <label class="form-label small subtle mb-1"><i class="fa fa-calendar ic fa-fw me-1"></i>Next follow-up (optional)</label>
                    <input type="datetime-local" name="next_call_at" class="form-control">
                  </div>
                  <div class="col-md-4 text-end">
                    <button class="btn-u btn-dark w-100"><i class="fa fa-paper-plane ic fa-fw"></i> Save</button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="card-white">
            <div class="card-head d-flex align-items-center justify-content-between">
              <div class="fw-semibold"><i class="fa fa-stream ic fa-fw me-2"></i>Comments Log</div>
              <div class="note"><?php echo e($lead->comments->count()); ?> items</div>
            </div>
            <div class="card-body">
              <div class="timeline">
                <?php $__empty_1 = true; $__currentLoopData = $lead->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <div class="tl-item">
                    <div class="d-flex justify-content-between">
                      <div class="fw-semibold"><?php echo e($c->user?->name ?? 'Unknown'); ?></div>
                      <div class="note"><?php echo e(\Carbon\Carbon::parse($c->created_at)->format('Y-m-d H:i')); ?></div>
                    </div>
                    <div class="mt-1"><?php echo e($c->comment); ?></div>
                    <form action="<?php echo e(route('dashboard.leads.comments.destroy',$c)); ?>" method="post" class="mt-2">
                      <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                      <button class="btn-u btn-outline-danger"><i class="fa fa-trash ic fa-fw"></i> Delete</button>
                    </form>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <div class="subtle">No comments yet.</div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        
        <div class="tab-pane fade" id="calls-pane" role="tabpanel" aria-labelledby="calls-tab">
          <div class="card-white mb-3">
            <div class="card-head d-flex align-items-center justify-content-between">
              <div class="fw-semibold"><i class="fa fa-phone ic fa-fw me-2"></i>Log Call</div>
              <?php if(!$locked): ?>
                <div class="d-none d-md-block note"><i class="fa fa-circle-info ic fa-fw me-1"></i>Logging a call will automatically set status to <b>Follow up</b>.</div>
              <?php else: ?>
                <div class="d-none d-md-block note"><i class="fa fa-lock ic fa-fw me-1"></i>Status is final — logging a call will not change it.</div>
              <?php endif; ?>
            </div>
            <div class="card-body">
              <form method="post" action="<?php echo e(route('dashboard.leads.calllogs.store',$lead)); ?>">
                <?php echo csrf_field(); ?>
                <div class="row g-2">
                  <div class="col-md-4">
                    <label class="form-label small subtle mb-1">Call time</label>
                    <input type="datetime-local" name="called_at" class="form-control" required>
                  </div>
                  <div class="col-md-2">
                    <label class="form-label small subtle mb-1">Duration (s)</label>
                    <input type="number" min="0" name="duration_sec" class="form-control" value="0">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label small subtle mb-1">Outcome</label>
                    <select name="outcome" class="form-select">
                      <option value="answered">Answered</option>
                      <option value="no_answer">No Answer</option>
                      <option value="busy">Busy</option>
                      <option value="wrong_number">Wrong Number</option>
                      <option value="voicemail">Voicemail</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label small subtle mb-1">Next follow-up</label>
                    <input type="datetime-local" name="next_call_at" class="form-control">
                  </div>
                  <div class="col-12">
                    <label class="form-label small subtle mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="form-control"></textarea>
                  </div>
                </div>
                <div class="mt-3 d-flex gap-2 justify-content-end">
                  <a class="btn-u btn-soft" href="tel:<?php echo e($lead->phone); ?>"><i class="fa fa-phone ic fa-fw"></i> Call Now</a>
                  <a class="btn-u btn-soft" href="mailto:<?php echo e($lead->email); ?>"><i class="fa fa-envelope ic fa-fw"></i> Send Email</a>
                  <button class="btn-u btn-dark"><i class="fa fa-floppy-disk ic fa-fw"></i> Save</button>
                </div>
              </form>
            </div>
          </div>

          <div class="card-white">
            <div class="card-head d-flex align-items-center justify-content-between">
              <div class="fw-semibold"><i class="fa fa-stream ic fa-fw me-2"></i>Calls Log</div>
              <div class="note"><?php echo e($lead->callLogs->count()); ?> items</div>
            </div>
            <div class="card-body">
              <div class="timeline">
                <?php $__empty_1 = true; $__currentLoopData = $lead->callLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <div class="tl-item">
                    <div class="d-flex justify-content-between">
                      <div class="fw-semibold"><?php echo e($l->user?->name ?? 'Unknown'); ?></div>
                      <div class="note"><?php echo e(\Carbon\Carbon::parse($l->called_at)->format('Y-m-d H:i')); ?></div>
                    </div>
                    <div class="note mt-1">
                      <i class="fa fa-stopwatch ic fa-fw me-1"></i><?php echo e($l->duration_sec); ?>s
                      <span class="mx-2">•</span>
                      <i class="fa fa-wave-square ic fa-fw me-1"></i><?php echo e($l->outcome); ?>

                    </div>
                    <?php if($l->notes): ?>
                      <div class="mt-1"><?php echo e($l->notes); ?></div>
                    <?php endif; ?>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <div class="subtle">No calls yet.</div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div> 
    </div>

    
    <div class="col-lg-4">
      <div class="sticky-sidebar">

        
        <div class="card-white mb-3">
          <div class="card-head d-flex align-items-center justify-content-between">
            <div class="fw-semibold"><i class="fa fa-qrcode ic fa-fw me-2"></i>QR Code</div>
            <?php if($lead->status === 'accept' && $qrImgUrl): ?>
              <a href="<?php echo e($qrImgUrl); ?>" download class="btn-u btn-dark">
                <i class="fa fa-download ic fa-fw"></i> Download
              </a>
            <?php endif; ?>
          </div>
          <div class="card-body text-center">
            <?php if($lead->status !== 'accept'): ?>
              <div class="subtle">QR will be available after <b>Accept</b>.</div>
            <?php elseif(!$qrImgUrl): ?>
              <div class="subtle">QR not found for this lead.</div>
            <?php else: ?>
              <img src="<?php echo e($qrImgUrl); ?>" alt="QR" class="qr-img img-fluid mb-2">
              <div class="d-flex justify-content-center gap-2">
                <a href="<?php echo e($qrImgUrl); ?>" target="_blank" class="btn-u btn-soft">
                  <i class="fa fa-up-right-from-square ic fa-fw"></i> Open
                </a>
                <a href="<?php echo e($qrImgUrl); ?>" download class="btn-u btn-dark">
                  <i class="fa fa-download ic fa-fw"></i> Download PNG
                </a>
              </div>
            <?php endif; ?>
          </div>
        </div>

        
        <div class="card-white mb-3">
          <div class="card-head"><div class="fw-semibold"><i class="fa fa-id-card ic fa-fw me-2"></i>Lead Summary</div></div>
          <div class="card-body">
            <div class="kv mb-2"><div class="k">Owner</div><div class="v"><?php echo e($lead->assignedUser?->name ?? 'Unassigned'); ?></div></div>
            <div class="kv mb-2"><div class="k">Source</div><div class="v"><?php echo e($lead->source ?: '—'); ?></div></div>
            <div class="kv mb-2"><div class="k">Job</div><div class="v"><?php echo e($lead->job ?: '—'); ?></div></div>
            <div class="kv mb-2"><div class="k">Created at</div><div class="v"><?php echo e($lead->created_at?->format('Y-m-d H:i')); ?></div></div>
            <div class="kv"><div class="k">Last update</div><div class="v"><?php echo e($lead->updated_at?->diffForHumans()); ?></div></div>
          </div>
        </div>

        
        <div class="card-white mb-3">
          <div class="card-head"><div class="fw-semibold"><i class="fa fa-calendar ic fa-fw me-2"></i>Set Next Follow-up</div></div>
          <div class="card-body">
            <form method="post" action="<?php echo e(route('dashboard.leads.update',$lead)); ?>">
              <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
              <input type="datetime-local" class="form-control mb-2"
                     name="next_call_at"
                     value="<?php echo e($lead->next_call_at ? \Carbon\Carbon::parse($lead->next_call_at)->format('Y-m-d\TH:i') : ''); ?>">
              
              <input type="hidden" name="name" value="<?php echo e($lead->name); ?>">
              <input type="hidden" name="email" value="<?php echo e($lead->email); ?>">
              <input type="hidden" name="phone" value="<?php echo e($lead->phone); ?>">
              <input type="hidden" name="job" value="<?php echo e($lead->job); ?>">
              <input type="hidden" name="status" value="<?php echo e($lead->status); ?>">
              <input type="hidden" name="assigned_user_id" value="<?php echo e($lead->assigned_user_id); ?>">
              <input type="hidden" name="source" value="<?php echo e($lead->source); ?>">
              <input type="hidden" name="notes" value="<?php echo e($lead->notes); ?>">
              <button class="btn-u btn-primary w-100"><i class="fa fa-calendar-check ic fa-fw"></i> Save</button>
            </form>
            <?php $overdue = $lead->next_call_at && \Carbon\Carbon::parse($lead->next_call_at)->lt(now()) && in_array($lead->status,['new','follow_up']); ?>
            <?php if($overdue): ?>
              <div class="danger-text mt-2"><i class="fa fa-triangle-exclamation ic fa-fw me-1"></i> Overdue follow-up</div>
            <?php elseif($lead->next_call_at): ?>
              <div class="ok-text mt-2"><i class="fa fa-bell ic fa-fw me-1"></i> <?php echo e(\Carbon\Carbon::parse($lead->next_call_at)->diffForHumans()); ?></div>
            <?php endif; ?>
          </div>
        </div>

        
        <div class="card-white">
          <div class="card-head"><div class="fw-semibold"><i class="fa fa-bolt ic fa-fw me-2"></i>Quick Actions</div></div>
          <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
              <?php if(!$locked): ?>
                <?php $__currentLoopData = ['new','follow_up','accept','reject','lost']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <form action="<?php echo e(route('dashboard.leads.status',$lead)); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="status" value="<?php echo e($st); ?>">
                    <button class="btn-u btn-soft"><?php echo e(ucfirst(str_replace('_',' ',$st))); ?></button>
                  </form>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php else: ?>
                <button class="btn-u btn-soft" data-bs-toggle="modal" data-bs-target="#statusLockedModal">
                  <i class="fa fa-lock ic fa-fw"></i> Status is final
                </button>
              <?php endif; ?>
              <a class="btn-icon" href="tel:<?php echo e($lead->phone); ?>" title="Call"><i class="fa fa-phone ic fa-fw"></i></a>
              <a class="btn-icon" href="mailto:<?php echo e($lead->email); ?>" title="Email"><i class="fa fa-envelope ic fa-fw"></i></a>
            </div>
          </div>
        </div>
      </div> 
    </div>

  </div> 
</div>


<div class="modal fade" id="statusLockedModal" tabindex="-1" aria-labelledby="statusLockedLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content card-white">
      <div class="card-head">
        <h6 id="statusLockedLabel" class="mb-0"><i class="fa fa-lock ic fa-fw me-1"></i> Status is final</h6>
      </div>
      <div class="card-body">
        <p class="mb-3">
          This lead’s status is <strong><?php echo e(ucfirst(str_replace('_',' ',$lead->status))); ?></strong>, therefore
          <span class="fw-bold">it cannot be changed here</span>.
        </p>
        <ul class="mb-3 subtle">
          <li>You can add comments and call logs without changing the status.</li>
          <li>If you need to reopen the status, ask an admin or enable it in server logic (if allowed).</li>
        </ul>
        <div class="text-end">
          <button type="button" class="btn-u btn-dark" data-bs-dismiss="modal">
            <i class="fa fa-check ic fa-fw"></i> Got it
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/customer/show.blade.php ENDPATH**/ ?>