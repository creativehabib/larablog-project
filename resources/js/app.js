import './bootstrap';
import 'jquery';
import 'pace-progress';
import '@fortawesome/fontawesome-free/js/all.js';
import feather from 'feather-icons';
import "toastr";
// পেজ প্রথমবার লোড হওয়ার জন্য
document.addEventListener('DOMContentLoaded', () => {
    feather.replace();
});

// Livewire পেজের কোনো অংশ আপডেট করা শেষ করলে এই ইভেন্টটি কাজ করবে
document.addEventListener('livewire:morph.end', () => {
    feather.replace();
});

