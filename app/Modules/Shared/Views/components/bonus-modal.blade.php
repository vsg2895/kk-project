<div class="modal" id="backtoschoolModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content backtoschoolModal-content">
            <div class="modal-header">
{{--                <h3 style="display: inline" class="modal-title" id="exampleModalLabel">Boka <b><a id="boka-har" href="/teoriprov-online">här</a></b></h3>--}}
                <button type="button" id="close_backtoschoolModal" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex justify-content-center">
{{--                <a href="/teoriprov-online" id="backtoschoolImg">--}}
                    <img src="{{asset('images/moped-am-popup.png')}}" alt="Korkortspaket">
{{--                </a>--}}
            </div>
        </div>
    </div>
</div>

<script>
    setTimeout(function () {
        if (getCookie('paketModal_is_shown') === null) {
            setCookie('paketModal_is_shown', 'false', 1);
        }
        var codeModal = document.getElementById('backtoschoolModal');
        if (getCookie('paketModal_is_shown') === 'false') {
            codeModal.style.display = "block";
            codeModal.style.top = "2 rem";
            codeModal.style.overflowY = "auto";
        }
        document.getElementById('close_backtoschoolModal').onclick = function() {
            codeModal.style.display = "none";
            setCookie('paketModal_is_shown', 'true', 1);
        }

        // document.getElementById('boka-har').onclick = function() {
        //     setCookie('paketModal_is_shown', 'true', 1);
        // }
    })

    function setCookie(name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }
</script>
