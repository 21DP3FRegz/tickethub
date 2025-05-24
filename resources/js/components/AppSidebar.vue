<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Music, Ticket, Settings } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

const page = usePage();

const user = computed(() => page.props.auth?.user);

// Check if user has admin role
const isAdmin = computed(() => {
    if (!user.value || !user.value.roles) return false;
    return user.value.roles.some(role => role.name === 'administrator');
});

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
        icon: LayoutGrid,
    },
    {
        title: 'Concerts',
        href: route('concerts.index'),
        icon: Music,
    },
    {
        title: 'My Tickets',
        href: route('bookings.index'),
        icon: Ticket,
    },
];

// Add admin link if user is admin
const footerNavItems = computed(() => {
    const items: NavItem[] = [];

    if (isAdmin.value) {
        items.push({
            title: 'Admin Panel',
            href: route('admin.dashboard'),
            icon: Settings,
        });
    }

    return items;
});

console.log('User:', user.value);
console.log('Is Admin:', isAdmin.value);
console.log('Footer Items:', footerNavItems.value);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
