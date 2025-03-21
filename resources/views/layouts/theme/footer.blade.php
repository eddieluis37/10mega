<div class="footer-wrapper">
   
    <div class="footer-section f-section-1">
        <p class="">ERP <a target="_blank" href="https://puracarnes.com"></a> Carnes Frias Mega</p>
    </div>
    @if (app()->environment('local', 'staging', 'testing'))
    <div class="alert alert-warning text-center m-0 py-2" role="alert">
        {{ config('app.env_label') }}
    </div>
    @endif
    <div class="footer-section f-section-2" style="display: flex; align-items: center;">
        <p class="" style="margin: 0;">V 1.1</p>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-heart" style="margin-left: 5px;">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
        </svg>
    </div>
    <div class="footer-section f-section-2" style="display: flex; align-items: center;">
        <p class="" style="margin: 0;">2025</p>
        <img src="{{asset('assets/img/mega-carnes-frias.svg')}}" alt="Icono de computadora" width="24" height="24" style="margin-left: 5px;">
    </div>
</div>