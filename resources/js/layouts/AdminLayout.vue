<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarInset, SidebarProvider, SidebarTrigger } from '@/components/ui/sidebar';
import { Separator } from '@/components/ui/separator';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    LayoutDashboard,
    Users,
    Music,
    MapPin,
    Calendar,
    LogOut
} from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';

const adminNavItems: NavItem[] = [
    { title: 'Dashboard', href: route('admin.dashboard'), icon: LayoutDashboard },
    { title: 'Users', href: route('admin.users.index'), icon: Users },
    { title: 'artists', href: route('admin.artists.index'), icon: Music },
    { title: 'Locations', href: route('admin.locations.index'), icon: MapPin },
    { title: 'Concerts', href: route('admin.concerts.index'), icon: Calendar },
];

const footerNavItems: NavItem[] = [
    { title: 'Back to App', href: route('dashboard'), icon: LogOut },
];
</script>

<template>
    <SidebarProvider>
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" as-child>
                            <Link :href="route('admin.dashboard')">
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain :items="adminNavItems" />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter :items="footerNavItems" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>

        <SidebarInset>
            <header class="flex h-16 shrink-0 items-center gap-2 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12">
                <div class="flex items-center gap-2 px-4">
                    <SidebarTrigger class="-ml-1" />
                    <Separator orientation="vertical" class="mr-2 h-4" />
                    <div class="text-sm font-medium text-muted-foreground">
                        Admin Panel
                    </div>
                </div>
            </header>

            <div class="flex flex-1 flex-col gap-4 p-4 pt-0">
                <slot />
            </div>
        </SidebarInset>
    </SidebarProvider>
</template>
