export default [
  {
    path: '/',
    component: () => import('../layouts/PublicLayout.vue'),
    children: [
      { path: '', name: 'home', component: () => import('../views/public/home/HomeView.vue') },
      { path: 'about', name: 'about', component: () => import('../views/public/about/AboutView.vue') },
      { path: 'committee', name: 'committee', component: () => import('../views/public/committee/CommitteeView.vue') },
      { path: 'election-commission', name: 'election', component: () => import('../views/public/election/ElectionView.vue') },
      { path: 'notices', name: 'notices', component: () => import('../views/public/notices/NoticesView.vue') },
      { path: 'notices/:id', name: 'notice-detail', component: () => import('../views/public/notices/NoticeDetailView.vue') },
      { path: 'news/:id', name: 'news-detail', component: () => import('../views/public/news/NewsDetailView.vue') },
      { path: 'join', name: 'join', component: () => import('../views/public/join/JoinView.vue') },
      { path: 'news', name: 'news', component: () => import('../views/public/news/NewsView.vue') },
      { path: 'gallery', name: 'gallery', component: () => import('../views/public/gallery/GalleryView.vue') },
      { path: 'constitution', name: 'constitution', component: () => import('../views/public/constitution/ConstitutionView.vue') },
      { path: 'members', name: 'members', component: () => import('../views/public/members/MembersView.vue') },
      { path: 'contact', name: 'contact', component: () => import('../views/public/contact/ContactView.vue') },
    ],
  },
]
