<template>
    <div>
        <!-- Hero Section -->
        <section class="bg-light text-center py-5">
            <div class="container">
                <h1 class="display-4 fw-bold">Welcome to Our App</h1>
                <p class="lead text-muted">A modern Laravel + Vue + Bootstrap boilerplate</p>
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <Link href="/login" class="btn btn-outline-primary btn-lg">Login</Link>
                    <Link href="/register" class="btn btn-primary btn-lg">Register</Link>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-4" v-for="(feature, i) in features" :key="i">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center">
                                <i :class="['bi', feature.icon, 'display-5', 'text-primary']"></i>
                                <h5 class="card-title mt-3">{{ feature.title }}</h5>
                                <p class="card-text text-muted">{{ feature.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="bg-primary text-white text-center py-5">
            <div class="container">
                <h2 class="fw-bold">Ready to get started?</h2>
                <p class="lead">Join the developer-friendly environment now.</p>
                <Link href="/register" class="btn btn-outline-light btn-lg mt-3">Register Now</Link>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-dark text-white text-center py-3 mt-5">
            <small>&copy; 2025 Laravel-Vue Boilerplate. All rights reserved.</small>
        </footer>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

const features = [
    {
        title: 'Laravel 12',
        description: 'Powerful and modern backend framework for web apps.',
        icon: 'bi-layers',
    },
    {
        title: 'Vue 3 + TS',
        description: 'Reactive frontend powered by Vue and TypeScript.',
        icon: 'bi-lightning-charge',
    },
    {
        title: 'Bootstrap 5',
        description: 'Beautiful and responsive UI with Bootstrap 5.',
        icon: 'bi-bootstrap',
    },
]
</script>
