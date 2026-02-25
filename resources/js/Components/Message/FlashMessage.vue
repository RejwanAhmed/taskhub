<script setup>
import { onUnmounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

toastr.options = {
  closeButton: false,
  progressBar: true,
  positionClass: "toast-top-right",
  showDuration: 300,
  hideDuration: 1000,
  timeOut: 5000,
  extendedTimeOut: 1000,
  showEasing: "swing",
  hideEasing: "linear",
  showMethod: "fadeIn",
  hideMethod: "fadeOut"
};

const page = usePage();

function showFlash() {
    const flash = page.props.flash;
    if (flash?.success) toastr.success(flash.success);
    else if (flash?.error) toastr.error(flash.error);
    else if (flash?.message) toastr.info(flash.message);
}

const removeListener = router.on('success', () => {
    showFlash();
});

onUnmounted(() => {
    removeListener();
});
</script>