<template>
    <div class="w-full h-full flex flex-col">
        <TransitionRoot as="template" :show="sidebarOpen">
            <Dialog as="div" class="relative z-50 lg:hidden" @close="sidebarOpen = false">
                <TransitionChild as="template" enter="transition-opacity ease-linear duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="transition-opacity ease-linear duration-300" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-900/80" />
                </TransitionChild>

                <div class="fixed inset-0 flex">
                    <TransitionChild as="template" enter="transition ease-in-out duration-300 transform" enter-from="-translate-x-full" enter-to="translate-x-0" leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0" leave-to="-translate-x-full">
                        <DialogPanel class="relative mr-16 flex w-full max-w-xs flex-1">
                            <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100" leave-to="opacity-0">
                                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                                    <button type="button" class="-m-2.5 p-2.5" @click="sidebarOpen = false">
                                        <span class="sr-only">Close sidebar</span>
                                        <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
                                    </button>
                                </div>
                            </TransitionChild>

                            <!-- Sidebar component, swap this element with another sidebar if you like -->
                            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white pb-2">
                                <div class="h-16 flex shrink-0 items-center justify-center border-b border-gray-200 text-gray-700 gap-2 font-semibold">
                                    <span class="text-sm uppercase">
                                        Limitless
                                    </span>
                                </div>

                                <nav class="flex flex-1 flex-col">
                                    <ul role="list" class="flex flex-1 flex-col px-6  gap-y-7">
                                        <li>
                                            <ul role="list" class="-mx-2 space-y-1">
                                                <li v-for="item in navigation" :key="item.name">
                                                    <template v-if="item.allowed">
                                                        <Link :href="item.href" :class="[item.current ? 'bg-primary-500 text-gray-50' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-700', 'group flex gap-x-3 rounded-md px-4 py-2 text-sm leading-6 font-medium']" @click="sidebarOpen = false">
                                                            <component :is="item.icon" :class="[item.current ? 'text-gray-100' : 'text-gray-400 group-hover:text-gray-700', 'h-6 w-6 shrink-0']" aria-hidden="true" />
                                                            {{ item.name }}
                                                        </Link>
                                                    </template>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>

        <!-- Static sidebar for desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <!-- Sidebar component, swap this element with another sidebar if you like -->
            <div class="flex grow flex-col overflow-y-auto border-r border-gray-200 bg-gray-800">
                <div class="h-16 flex shrink-0 items-center justify-start text-gray-50 gap-2 font-semibold">
                    <span class="px-10 text-sm uppercase text-gray-400">
                        Med.Exchange
                    </span>
                </div>

                <v-content-body class="h-full">
                    <nav class="h-full flex flex-1 flex-col">
                        <ul role="list" class="flex flex-1 flex-col gap-8">
                            <li>
                                <ul role="list" class="space-y-1">
                                    <li v-for="item in navigation" :key="item.name">
                                        <template v-if="item.allowed">
                                            <Link :href="item.href" :class="[item.current ? 'bg-primary-500 text-gray-50' : 'text-gray-300 hover:bg-gray-700 hover:text-gray-50', 'group flex gap-x-3 rounded-md px-4 py-2 text-sm leading-6 font-medium']">
                                                <component :is="item.icon" :class="[item.current ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300', 'h-6 w-6 shrink-0']" aria-hidden="true" />
                                                {{ item.name }}
                                            </Link>
                                        </template>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </v-content-body>
            </div>
        </div>

        <div class="h-16 shrink-0 z-40 flex items-center gap-x-6 bg-white px-4 sm:px-6 border-b border-gray-200">
            <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="sidebarOpen = true">
                <span class="sr-only">Open sidebar</span>
                <Bars3Icon class="h-6 w-6" aria-hidden="true" />
            </button>
            
            <div class="flex-1 text-sm font-semibold leading-6 text-gray-900">
                <!-- Page Title -->
            </div>
                                
            <Link :href="route('panel.me.profile.edit')" class="hidden md:flex items-center justify-center gap-4 text-sm font-medium text-gray-600 hover:text-primary-500 border-r-2 border-gray-200 pr-6">
                <span>{{ $page.props.auth.user.name }}</span>
            </Link>

            <template v-if="$page.props.auth.isImpersonated">
                <Link :href="route('impersonate.leave')" method="get" as="button" class="flex items-center justify-between gap-2 text-sm font-medium text-red-500 hover:text-red-600 border-r-2 border-gray-200 pr-6">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
                    </svg>

                    <span class="hidden md:inline">
                        Leave Impersonation
                    </span>
                </Link>
            </template>

            <Link :href="route('panel.me.profile.edit')" class="flex items-center justify-between gap-2 text-sm font-medium text-gray-600 hover:text-primary-500 border-r-2 border-gray-200 pr-6">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M7.84 1.804A1 1 0 0 1 8.82 1h2.36a1 1 0 0 1 .98.804l.331 1.652a6.993 6.993 0 0 1 1.929 1.115l1.598-.54a1 1 0 0 1 1.186.447l1.18 2.044a1 1 0 0 1-.205 1.251l-1.267 1.113a7.047 7.047 0 0 1 0 2.228l1.267 1.113a1 1 0 0 1 .206 1.25l-1.18 2.045a1 1 0 0 1-1.187.447l-1.598-.54a6.993 6.993 0 0 1-1.929 1.115l-.33 1.652a1 1 0 0 1-.98.804H8.82a1 1 0 0 1-.98-.804l-.331-1.652a6.993 6.993 0 0 1-1.929-1.115l-1.598.54a1 1 0 0 1-1.186-.447l-1.18-2.044a1 1 0 0 1 .205-1.251l1.267-1.114a7.05 7.05 0 0 1 0-2.227L1.821 7.773a1 1 0 0 1-.206-1.25l1.18-2.045a1 1 0 0 1 1.187-.447l1.598.54A6.992 6.992 0 0 1 7.51 3.456l.33-1.652ZM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
                </svg>
                
                <span class="hidden md:inline">
                    Settings
                </span>
            </Link>

            <Link :href="route('auth.logout')" method="post" as="button" class="flex items-center justify-between gap-2 text-sm font-medium text-gray-600 hover:text-primary-500">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M10 2a.75.75 0 0 1 .75.75v7.5a.75.75 0 0 1-1.5 0v-7.5A.75.75 0 0 1 10 2ZM5.404 4.343a.75.75 0 0 1 0 1.06 6.5 6.5 0 1 0 9.192 0 .75.75 0 1 1 1.06-1.06 8 8 0 1 1-11.313 0 .75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                </svg>

                <span class="hidden md:inline">
                    Sign Out
                </span>
            </Link>
        </div>

        <main class="h-full lg:pl-72">
            <slot />
        </main>

        <!--
            @todo Future Development: Aside Column
        -->
        <template v-if="false">
            <main class="lg:pl-72">
                <div class="xl:pl-96">
                    <slot></slot>
                </div>
            </main>

            <aside class="fixed inset-y-0 left-72 hidden w-96 overflow-y-auto border-r border-gray-200 px-4 py-6 sm:px-6 lg:px-8 xl:block">
                <slot name="aside"></slot>
            </aside>
        </template>
    </div>
</template>
  
<script type="text/javascript">
import BugsnagPluginVue from "@bugsnag/plugin-vue";
import { Link } from "@inertiajs/vue3";
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from "@headlessui/vue";
import { Bars3Icon, XMarkIcon } from "@heroicons/vue/24/outline";
import {
    ComputerDesktopIcon,
    ListBulletIcon,
    CalendarDaysIcon,
    ScaleIcon,
    BuildingOfficeIcon,
    HeartIcon,
    RectangleStackIcon,
    ClipboardDocumentListIcon,
    BriefcaseIcon,
    IdentificationIcon,
    GiftTopIcon,
    CubeTransparentIcon,
    FingerPrintIcon,
    MapIcon,
    UserGroupIcon,
    BuildingOffice2Icon,
} from "@heroicons/vue/24/solid";

export default {
    name: "PanelLayout",
    components: {
        Link,
        Dialog,
        DialogPanel,
        TransitionChild,
        TransitionRoot,
        Bars3Icon,
        XMarkIcon,
    },
    data() {
        return {
            sidebarOpen: false,
        };
    },
    computed: {
        navigation() {
            return [
                { name: "Dashboard",      href: route("panel.dashboard"),            current: this.isLinkActive(route("panel.dashboard")),            allowed: true,                                                               icon: ComputerDesktopIcon },
                { name: "Activity Log",   href: route("panel.activity-log.index"),   current: this.isLinkActive(route("panel.activity-log.index")),   allowed: this.$auth().can(["activity-log.list", "activity-log.list-own"]),   icon: ListBulletIcon },
                { name: "Appointments",   href: route("panel.appointments.index"),   current: this.isLinkActive(route("panel.appointments.index")),   allowed: this.$auth().can(["appointment.list", "appointment.list-own"]),     icon: CalendarDaysIcon },
                { name: "Attorneys"   ,   href: route("panel.attorneys.index"),      current: this.isLinkActive(route("panel.attorneys.index")),      allowed: this.$auth().can(["attorney.list", "attorney.list-own"]),           icon: ScaleIcon },
                { name: "Clinics",        href: route("panel.clinics.index"),        current: this.isLinkActive(route("panel.clinics.index")),        allowed: this.$auth().can(["clinic.list", "clinic.list-own"]),               icon: BuildingOfficeIcon },
                { name: "Doctors"   ,     href: route("panel.doctors.index"),        current: this.isLinkActive(route("panel.doctors.index")),        allowed: this.$auth().can(["doctor.list", "doctor.list-own"]),               icon: HeartIcon },
                { name: "Document Categories", href: route("panel.document-categories.index"), current: this.isLinkActive(route("panel.document-categories.index")), allowed: this.$auth().can(["document-category.list", "document-category.list-own"]), icon: RectangleStackIcon },
                { name: "Document Types", href: route("panel.document-types.index"), current: this.isLinkActive(route("panel.document-types.index")), allowed: this.$auth().can(["document-type.list", "document-type.list-own"]), icon: ClipboardDocumentListIcon },
                { name: "Law Firms",      href: route("panel.law-firms.index"),      current: this.isLinkActive(route("panel.law-firms.index")),      allowed: this.$auth().can(["law-firm.list", "law-firm.list-own"]),           icon: BriefcaseIcon },
                { name: "Organizations",  href: route("panel.organizations.index"),  current: this.isLinkActive(route("panel.organizations.index")),  allowed: this.$auth().can(["organization.list", "organization.list-own"]),  icon: BuildingOffice2Icon },
                { name: "Patients",       href: route("panel.patients.index"),       current: this.isLinkActive(route("panel.patients.index")),       allowed: this.$auth().can(["patient.list", "patient.list-own"]),             icon: IdentificationIcon },
                { name: "Referrals",      href: route("panel.referrals.index"),      current: this.isLinkActive(route("panel.referrals.index")),      allowed: this.$auth().can(["referral.list", "referral.list-own"]),           icon: GiftTopIcon },
                { name: "Referral Types", href: route("panel.referral-types.index"), current: this.isLinkActive(route("panel.referral-types.index")), allowed: this.$auth().can(["referral-type.list", "referral-type.list-own"]), icon: CubeTransparentIcon },
                { name: "Roles",          href: route("panel.roles.index"),          current: this.isLinkActive(route("panel.roles.index")),          allowed: this.$auth().can(["role.list", "role.list-own"]),                   icon: FingerPrintIcon },
                { name: "States",         href: route("panel.states.index"),         current: this.isLinkActive(route("panel.states.index")),         allowed: this.$auth().can(["state.list", "state.list-own"]),                 icon: MapIcon },
                { name: "Users",          href: route("panel.users.index"),          current: this.isLinkActive(route("panel.users.index")),          allowed: this.$auth().can(["user.list", "user.list-own"]),                   icon: UserGroupIcon },
            ];
        },
    },
    methods: {
        getPathName(str) {
            return (new URL(str)).pathname;
        },
        isLinkActive(href) {
            return this.$page.url.startsWith(this.getPathName(href));
        },
    },
    created() {
        this.$bugsnag.start({
            appVersion: import.meta.env.VITE_APP_BUGSNAG_APP_VERSION,
            apiKey: import.meta.env.VITE_APP_BUGSNAG_API_KEY,
            releaseStage: import.meta.env.VITE_APP_BUGSNAG_RELEASE_VERSION,
            plugins: [new BugsnagPluginVue()],
            onError: function (event) {
                event.setUser('3', this.$page.props.auth.user.email, this.$page.props.auth.user.name)
            },
        });
    },
};
</script>
