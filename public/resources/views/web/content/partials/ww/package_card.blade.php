<div class="col-sm-6 col-md-4 mb-4 package-item">
    <div class="pricing-modern pricing-modern-primary">
        <div class="pricing-modern-header">
            <p class="pricing-modern-price heading-3">{{ $locale === 'ar' ? $package->name_ar : $package->name_en }}</p>
        </div>
        <div class="pricing-modern-body">
            <ul class="pricing-modern-list">
                @foreach($listItems as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
            <button class="button button-primary" type="button" data-triangle=".button-overlay">
                <span>{{ __('Become Sponsor') }}</span>
                <span class="button-overlay"></span>
            </button>
        </div>
    </div>
</div>
