@if (Auth::check())
    <!-- TriggerBee visitor identity -->
    <script>
        if (window.hasOwnProperty('mtr')) {
            mtr.session = {
                email: "{{ $user ?? $user->email }}",
                name: "{{ $user ?? $user->name }}",
                telephone: "{{ $user ?? $user->phone_number }}",
                title: "{{ $user ?? $user->getRoleName() }}",
                @if($user->isOrganizationUser())
                organization: "{{ $user ?? $user->organization->name }}"
                @endif
            };
        }
    </script>
    <!-- End TriggerBee visitor identity -->
@endif
