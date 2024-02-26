export default [
    {
        path: '/login',
        name: 'login',
        meta: {
            middleware: "guest"
        },
        component: () =>
            import ('../views/pages/auth/login.vue')
    },
    {
        path: '/register',
        name: 'register',
        meta: {
            middleware: "guest"
        },
        component: () =>
            import ('../views/pages/auth/register.vue')
    },
    {
        path: '/',
        name: 'home',
        meta: {
            middleware: "auth"
        },
        component: () =>
            import ('../views/pages/dashboard/index.vue')
    },
    {
        path: '/chat',
        name: 'Chat',
        meta: {
            middleware: "auth"
        },
        component: () =>
            import ('../views/pages/chat/index.vue')
    },
]