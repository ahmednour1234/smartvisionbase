<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">

<section class="sliding-text-one">
    <div class="sliding-text-one__wrap" style="background-image: url('{{ asset('assets/images/bg/parallax-bg.jpg') }}'); background-attachment: fixed; background-size: cover; background-position: center;">
        <div class="sliding-text-one__inner">
            <h2 class="sliding-text__title">
                This Event Is A Private Event
            </h2>
        </div>
    </div>
</section>

<style>
.sliding-text-one {
    background: linear-gradient(90deg, #3c2a1e 0%, #1a120b 70%);
    padding: 0;
    z-index: 5;
}

@media (max-width: 767px) {
  .sliding-text-one {
    margin-top: 0px
  }
}

.sliding-text-one__wrap {
    overflow: hidden;
    padding: 40px 0;
    position: relative;
    color: #fff;
}

.sliding-text__title {
    font-family: 'Montserrat', sans-serif;
    font-size: 2.5rem;
    font-weight: bold;
    white-space: nowrap;
    color: gold;
    display: inline-block;
    animation: slideLoop 10s linear infinite;
    padding-left: 100%; /* يبدأ من أقصى اليمين */
}

/* الحركة */
@keyframes slideLoop {
    0% {
        transform: translateX(0); /* البداية من اليمين */
    }
    100% {
        transform: translateX(-100%); /* يختفي شمال */
    }
}

/* طبقة شفافة فوق الخلفية */
.sliding-text-one__wrap::before {
    content: "";
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1;
}

.sliding-text-one__inner {
    position: relative;
    z-index: 2;
}

@media (max-width: 767.5px) {
    .sliding-text__title {
        font-size: 30px;
    }
}
</style>
