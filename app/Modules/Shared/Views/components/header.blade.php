@php
$isRegisterPage = Request::url() === route('auth::register.show') ||
                  Request::url() === route('auth::register.organization') ||
                  Request::url() === route('auth::register.student');
$isIndexpage = Request::url() === route('shared::index.index');
$isLoginPage = Request::url() === route('auth::login');
@endphp

<header id="page-header" :class="{ collapsed: isCollapsed }">
    <div class="page-header-inner">
        <div class="hidden-lg-up py-1" :class="{ 'scroll-trigger-wrapper': !isCollapsed }">
            <div class="d-flex align-items-center justify-content-between">
                <a href="{{ route('shared::index.index') }}" class="brand-logo"></a>
                <div :class="{ collapsed: isCollapsed }" class="right-mobile-container header-item">
                    <button @click="toCheckoutPage" class="cart-btn-container mr-2">
                        <img src="{{ asset('build/img/cart.png') }}" alt="Shopping cart image">
                        <span v-show="qty > 0" v-html="qty" id="cart-items-count"></span>
                    </button>
                    <button @click="isCollapsed =! isCollapsed" type="button" class="navbar-toggler">
                        <i v-if="isCollapsed" class="far fa-times-circle ico-menu-circle"></i>
                        <i v-else class="fa fa-bars"></i>
                    </button>
                </div>
            </div>

            @if(auth()->check() && auth()->user()->isStudent())
                <div class="item-text hidden-md-up float-right">{{ auth()->user()->given_name ?:  auth()->user()->email }}
                    @if(auth()->user()->isStudent() && (float)auth()->user()->amount > 0)
                        | Saldo: {{ auth()->user()->amount }}
                    @endif
                </div>
            @endif
            <img v-show="isCollapsed" class="road-menu-img" src="{{ asset('build/svg/road-menu-small.svg') }}" />
        </div>

        {{--banner--}}
        <div style="margin-top: 50px;" class="hidden-md-up">
            <div style="max-width: 100%; max-height: 115px; overflow: hidden;">
                <promo-banner></promo-banner>
                <img style="height: 32px" src="{{asset('build/img/swish-logo.svg')}}" alt="swish-logo">
            </div>
        </div>
        {{--only logo--}}
{{--        <div style="margin-top: 50px; display: flex; justify-content: center;" class="hidden-md-up">
            <div style="max-width: 100%; overflow: hidden; margin: 10px 0 10px 0;">
                <img :src="'/images/klarna-logo.svg'" alt="Klarna logo">
            </div>
        </div>--}}

        <a href="{{ route('shared::index.index') }}" class="hidden-md-down brand-logo"> </a>
        <div class="navbar-container hidden-md-down">
            <search-bar v-if="isCollapsed" class="my-1"></search-bar>
            @if(auth()->check())
            <div class="navbar-container-inner authorized">
                    @if(auth()->user()->isStudent())
                    <ul class="nav navbar-nav p-0 {{ starts_with(Route::current()->uri(), 'student') ? 'hidden-md-up' : '' }}">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('student::bookings.index') }}">
                                @icon('course')
                                <span>Mina kurser</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('student::orders.index') }}">
                                @icon('order')
                                <span>Beställningar</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('student::ratings.index') }}">
                                @icon('rating')
                                <span>Betyg</span>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('student::user.show') }}">
                                @icon('user')
                                <span>Användarprofil</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('student::giftcard.index') }}">
                                @icon('gift-red')
                                <span>Presentkort</span>
                            </a>
                        </li>
                    </ul>
                    @elseif(auth()->user()->isAdmin())
                    <ul class="nav navbar-nav p-0 {{ starts_with(Route::current()->uri(), 'admin') ? 'hidden-md-up' : '' }}">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin::dashboard') }}">
                                @icon('home')
                                <span>Översikt</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin::orders.index') }}">
                                @icon('order')
                                <span>Beställningar</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin::courses.index') }}">
                                @icon('course')
                                <span>Kurser</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin::ratings.index') }}">
                                @icon('rating')
                                <span>Betyg</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin::statistics.index') }}">
                                @icon('statistics')
                                <span>Statistik</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin::schools.index') }}">
                                @icon('school')
                                <span>Trafikskolor</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin::organizations.index') }}">
                                @icon('organization')
                                <span>Organisationer</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin::users.index') }}">
                                @icon('users')
                                <span>Användare</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin::pages.index') }}">
                                @icon('pages-edit')
                                <span>Sidor</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin::users.show', ['id' => auth()->user()->id]) }}">
                                @icon('user')
                                <span>Användarprofil</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin::gift_cards.index') }}">
                                @icon('gift-red')
                                <span>Presentkort</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('blog::posts.indexAdmin') }}">
                                @icon('blog')
                                <span>Blogg/Nyheter</span>
                            </a>
                        </li>
                    </ul>
                    @elseif(auth()->user()->isOrganizationUser())
                    <ul class="nav navbar-nav p-0 {{ starts_with(Route::current()->uri(), 'organization') ? 'hidden-md-up' : '' }}">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('organization::dashboard') }}">
                                @icon('home')
                                <span>Översikt</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('organization::courses.index') }}">
                                @icon('course')
                                <span>Kurser</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('organization::orders.index') }}">
                                @icon('order')
                                <span>Beställningar</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('organization::ratings.index') }}">
                                @icon('rating')
                                <span>Betyg</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('organization::schools.index') }}">
                                @icon('school')
                                <span>Trafikskolor</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('organization::organization.show') }}">
                                @icon('organization')
                                <span>Organisation</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('organization::user.show') }}">
                                @icon('user')
                                <span>Användarprofil</span>
                            </a>
                        </li>
                    </ul>
                    @endif
                <div class="logout-btn-container justify-content-start justify-content-md-end">
                    <a class="btn btn-black btn-sm" href="{{ route('auth::logout') }}">Logga ut</a>
                </div>
            </div>
            <hr class="mx-2 hidden-md-down">
            @else
            <div class="nav-item d-flex hidden-lg-up mb-3">
                <a class="btn btn-black" href="{{ route('auth::login') }}">Logga in</a>
            </div>
            @endif
            <div class="navbar-container-inner">
                <div class="d-flex">
                    <ul class="nav navbar-nav p-0">
                        <li class="large-font-size none-popup-semantic-dropdown nav-item pr-1">
                            <semantic-dropdown inline="true" align-dropdown="right" should-skip-initial="true"
                                            floating="true"
                                            initial-item='{{ route('shared::introduktionskurs') }}'
                                            custom-action="true" :on-item-selected="navigationChanged">
                                <template slot="custom-dropdown-items">
                                    <div class="item" data-value="{{ route('shared::introduktionskurs') }}">
                                        <div class="item-text font-weight-bold-lg-up">{{ trans('vehicle_segments.introduction_car') }}</div>
                                    </div>
                                    <div class="item" data-value="{{ route('shared::search.courses', ['slug' => 'introduktionskursenglish']) }}">
                                        <div class="item-text font-weight-bold-lg-up">{{ trans('vehicle_segments.introduktionskurs_english') }}</div>
                                    </div>
                                </template>
                            </semantic-dropdown>
                        </li>
                        <li class="large-font-size none-popup-semantic-dropdown nav-item pr-1">
                            <semantic-dropdown inline="true" align-dropdown="right" should-skip-initial="true"
                                            floating="true"
                                            initial-item='{{ route('shared::riskettan') }}'
                                            custom-action="true" :on-item-selected="navigationChanged">
                                <template slot="custom-dropdown-items">
                                    <div class="item" data-value="{{ route('shared::riskettan') }}">
                                        <div class="item-text font-weight-bold-lg-up">Riskettan</div>
                                    </div>
                                    <div class="item" data-value="{{ route('shared::search.courses', ['slug' => 'riskettan-arabiska']) }}">
                                        <div class="item-text font-weight-bold-lg-up">Riskettan Arabiska</div>
                                    </div>
                                    <div class="item" data-value="{{ route('shared::engelskriskettan') }}">
                                        <div class="item-text font-weight-bold-lg-up">Risk1 English</div>
                                    </div>
                                    <div class="item" data-value="{{ route('shared::search.courses', ['slug' => 'riskettanspanish']) }}">
                                        <div class="item-text font-weight-bold-lg-up">{{ trans('vehicle_segments.spanish_risk_one') }}</div>
                                    </div>
                                </template>
                            </semantic-dropdown>
                        </li>
                        <li class="large-font-size none-popup-semantic-dropdown nav-item pr-1">
                            <semantic-dropdown inline="true" align-dropdown="right" should-skip-initial="true"
                                            floating="true"
                                            initial-item='{{ route('shared::risktvaan') }}'
                                            custom-action="true" :on-item-selected="navigationChanged">
                                <template slot="custom-dropdown-items">
                                    <div class="item" data-value="{{ route('shared::risktvaan') }}">
                                        <div class="item-text font-weight-bold-lg-up">Risktvåan</div>
                                    </div>
                                    <div class="item" data-value="{{ route('shared::search.courses', ['slug' => 'risktvaan-english']) }}">
                                        <div class="item-text font-weight-bold-lg-up">Risktvåan English</div>
                                    </div>
                                </template>
                            </semantic-dropdown>
                        </li>
                        <li class="large-font-size none-popup-semantic-dropdown nav-item pr-1">
                            <semantic-dropdown inline="true" align-dropdown="right" should-skip-initial="true"
                                            floating="true"
                                            initial-item='{{ route('shared::search.courses', ['slug' => 'risk1+2']) }}'
                                            custom-action="true" :on-item-selected="navigationChanged">
                                <template slot="custom-dropdown-items">
                                    <div class="item" data-value="{{ route('shared::search.courses', ['slug' => 'risk1+2']) }}">
                                        <div class="item-text font-weight-bold-lg-up">{{ trans('vehicle_segments.risk_one_two_combo') }}</div>
                                    </div>
                                    <div class="item" data-value="{{ route('shared::search.courses', ['slug' => 'risk1+2-english']) }}">
                                        <div class="item-text font-weight-bold-lg-up">{{ trans('vehicle_segments.risk_one_two_combo_english') }}</div>
                                    </div>
                                </template>
                            </semantic-dropdown>
                        </li>
                        <li class="nav-item pr-1">
                            <a class="nav-link large-font-size font-weight-bold-lg-up" href="{{ route('shared::teorilektion.paket') }}" title="Paket">Körkortspaket</a>
                        </li>
                        <li class="nav-item pr-1">
                            <a class="nav-link large-font-size font-weight-bold-lg-up" href="{{ route('shared::teoriprov-online') }}" title="Körkortsteori och Testprov">Körkortsteori och Testprov</a>
                        </li>
                        <li class="nav-item pr-1">
                            <a class="nav-link large-font-size font-weight-bold-lg-up" href="{{ route('shared::search.courses', ['slug' => 'mopedam']) }}" title="Moped AM">Moped AM</a>
                        </li>
                        <div class="mobile-menu-divider"></div>
                        <li class="nav-item pr-1">
                            <a class="nav-link" href="{{ route('shared::teorilektion') }}" title="Körlektioner">Körlektioner</a>
                        </li>

                        <li class="nav-item pr-1">
                            <a class="nav-link" href="{{ route('shared::schools.index') }}" title="Trafikskolor">Jämför
                                trafikskolor</a>
                        </li>
                        <li class="nav-item pr-1">
                            <a class="nav-link" href="{{ route('shared::gift_card.index') }}" title="Presentkort">Presentkort</a>
                        </li>
                        <li class="nav-item pr-1">
                            <a class="nav-link" href="{{ route('shared::riskettanmc') }}" title="Riskettan MC">Riskettan MC</a>
                        </li>
                        <li class="nav-item pr-1">
                            <a class="nav-link" href="{{ route('shared::risktvaanmc') }}" title="Risktvåan MC">Risktvåan MC</a>
                        </li>

                        <li class="none-popup-semantic-dropdown reset-carret nav-item pr-1">
                            <semantic-dropdown inline="true" align-dropdown="right" should-skip-initial="true"
                                            floating="true" header="true"
                                            placeholder="YKB Förarutbildning"
                                            initial-item='{{ route('shared::search.courses', ['slug' => 'ykb_35_h']) }}'
                                            custom-action="true" :on-item-selected="navigationChanged">
                                <template slot="custom-dropdown-items">
                                    <div class="item" data-value="{{ route('shared::search.courses', ['slug' => 'ykb_140_h']) }}">
                                        <div class="item-text">{{ trans('vehicle_segments.ykb_grundkurs_140_h') }}</div>
                                    </div>
                                    <div class="item" data-value="{{ route('shared::search.courses', ['slug' => 'ykb_35_h']) }}">
                                        <div class="item-text">{{ trans('vehicle_segments.ykb_fortutbildning_35_h') }}</div>
                                    </div>
                                </template>
                            </semantic-dropdown>
                        </li>
                        <li class="nav-item pr-1">
                            <a class="nav-link" href="{{ route('blog::index') }}" title="Blogg/Nyheter">Blogg/Nyheter</a>
                        </li>
                    </ul>
                    <button v-show="qty > 0" @click="toCheckoutPage" class="cart-btn-container hidden-md-down">
                        <img src="{{ asset('build/img/cart.png') }}" alt="Shopping cart image">
                        <span v-html="qty" id="cart-items-count"></span>
                    </button>
                </div>

                <div class="nav-login mt-2 hidden-md-down">
                    <search-bar class="header-search"></search-bar>
                    <ul class="nav navbar-nav d-flex">
                        @if(auth()->check())
                            <li class="none-popup-semantic-dropdown nav-item nav-user text-primary">
                                <span>Inloggad som</span>
                                <semantic-dropdown inline="true" align-dropdown="right" should-skip-initial="true"
                                                   floating="true"
                                                   initial-item='{{ \Jakten\Helpers\Roles::getDashboardRouteForUser(auth()->user()) }}/'
                                                   custom-action="true" :on-item-selected="navigationChanged">
                                    <template slot="custom-dropdown-items">

                                        <div class="item"
                                             data-value="{{ \Jakten\Helpers\Roles::getDashboardRouteForUser(auth()->user()) }}/">
                                            <div
                                                class="item-text">{{ auth()->user()->given_name ?:  auth()->user()->email }}@if(auth()->user()->isStudent() && (float)auth()->user()->amount > 0)
                                                    | Saldo: {{ auth()->user()->amount }}@endif</div>
                                        </div>

                                        @if(auth()->user()->isStudent())
                                            <div class="item" data-value="{{ route('student::bookings.index') }}">
                                                <div class="item-text">Mina kurser</div>
                                            </div>
                                            <div class="item" data-value="{{ route('student::orders.index') }}">
                                                <div class="item-text">Beställningar</div>
                                            </div>
                                            <div class="item" data-value="{{ route('student::ratings.index') }}">
                                                <div class="item-text">Betyg</div>
                                            </div>
                                            <div class="item" data-value="{{ route('student::user.show') }}">
                                                <div class="item-text">Användarprofil</div>
                                            </div>
                                            <div class="item" data-value="{{ route('student::giftcard.index') }}">
                                                <div class="item-text">Presentkort</div>
                                            </div>
                                        @elseif(auth()->user()->isAdmin())
                                            <div class="item" data-value="{{ route('admin::dashboard') }}">
                                                <div class="item-text">Översikt</div>
                                            </div>
                                            <div class="item" data-value="{{ route('admin::orders.index') }}">
                                                <div class="item-text">Beställningar</div>
                                            </div>
                                            <div class="item" data-value="{{ route('admin::courses.index') }}">
                                                <div class="item-text">Kurser</div>
                                            </div>
                                            <div class="item" data-value="{{ route('admin::ratings.index') }}">
                                                <div class="item-text">Betyg</div>
                                            </div>
                                            <div class="item" data-value="{{ route('admin::statistics.index') }}">
                                                <div class="item-text">Statistik</div>
                                            </div>
                                            <div class="item" data-value="{{ route('admin::schools.index') }}">
                                                <div class="item-text">Trafikskolor</div>
                                            </div>
                                            <div class="item" data-value="{{ route('admin::organizations.index') }}">
                                                <div class="item-text">Organisationer</div>
                                            </div>
                                            <div class="item" data-value="{{ route('admin::users.index') }}">
                                                <div class="item-text">Användare</div>
                                            </div>
                                            <div class="item" data-value="{{ route('admin::pages.index') }}">
                                                <div class="item-text">Sidor</div>
                                            </div>
                                            <div class="item"
                                                 data-value="{{ route('admin::users.show', ['id' => auth()->user()->id]) }}">
                                                <div class="item-text">Användarprofil</div>
                                            </div>
                                        @elseif(auth()->user()->isOrganizationUser())
                                            <div class="item" data-value="{{ route('organization::dashboard') }}">
                                                <div class="item-text">Översikt</div>
                                            </div>
                                            <div class="item" data-value="{{ route('organization::courses.index') }}">
                                                <div class="item-text">Kurser</div>
                                            </div>
                                            <div class="item" data-value="{{ route('organization::orders.index') }}">
                                                <div class="item-text">Beställningar</div>
                                            </div>
                                            <div class="item" data-value="{{ route('organization::ratings.index') }}">
                                                <div class="item-text">Betyg</div>
                                            </div>
                                            <div class="item"
                                                 data-value="{{ route('organization::statistics.index') }}">
                                                <div class="item-text">Statistik</div>
                                            </div>
                                            <div class="item" data-value="{{ route('organization::schools.index') }}">
                                                <div class="item-text">Trafikskolor</div>
                                            </div>
                                            <div class="item"
                                                 data-value="{{ route('organization::organization.show') }}">
                                                <div class="item-text">Organisation</div>
                                            </div>
                                            <div class="item" data-value="{{ route('organization::user.show') }}">
                                                <div class="item-text">Användarprofil</div>
                                            </div>
                                            @if(Session::get('adminId'))
                                                <div class="item"
                                                     data-value="{{ route('organization::user.login.back') }}">
                                                    <div class="item-text">Login back</div>
                                                </div>
                                            @endif
                                        @endif
                                        <div class="item" data-value="{{ route('auth::logout') }}">
                                            <div class="item-text">Logga ut</div>
                                        </div>
                                    </template>
                                </semantic-dropdown>
                            </li>
                        @elseif($isIndexpage && !$isRegisterPage )
                            <li class="nav-item mr-1">
                                <a class="btn btn-outline-dark" href="{{ route('auth::register.student') }}">För dig som
                                    elev</a>
                            </li>
                            <li class="nav-item mr-1">
                                <a class="btn btn-outline-dark" href="{{ route('auth::register.organization') }}">För
                                    dig som trafikskola</a>
                            </li>
                        @elseif(!$isRegisterPage )
                            <li class="nav-item">
                                <a class="btn btn-outline-dark mr-1" href="{{ route('auth::register.show') }}">Registrering</a>
                            </li>
                        @endif
                        @if(!auth()->check() && !$isLoginPage)
                            <div class="nav-item">
                                <a class="btn btn-black" href="{{ route('auth::login') }}">Logga in</a>
                            </div>
                        @endif
                    </ul>
                </div>
                {{--banner--}}
                <div class="hidden-md-down d-flex justify-content-between">
                    <div style="max-width: calc(100vw - 900px); overflow: hidden">
                        <promo-banner></promo-banner>
                    </div>
                    <img style="height: 32px; margin: auto 5px;" src="{{asset('build/img/swish-logo.svg')}}" alt="swish-logo">
                </div>
                {{--only logo--}}
<!--                <div class="hidden-md-down" style="width: 66%;">
                    <div style="display: flex; justify-content: center; margin-top: 15px;">
                        <img style="height: 80px;" :src="'/images/klarna-logo-shopping.jpg'" alt="Klarna logo">
                    </div>
                </div>-->

            </div>
        </div>
    </div>
</header>
