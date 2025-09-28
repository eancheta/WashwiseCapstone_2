import { h as Vue, ref, computed } from "vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";

const sidebarOpen = ref(false);
function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value;
}

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const shop = computed(() => page.props.shop ?? null);

// ✅ Forced toggle functions
function closeShop(id) {
    router.post(`/owner/shop/${id}/close`);
}
function openShop(id) {
    router.post(`/owner/shop/${id}/open`);
}

export default {
    name: "CarWashOwnerDashboard",
    setup() {
        return () => Vue("div", { class: "w-full" }, [
            Vue(Head, { title: "Owner Dashboard" }),

            // -----------------
            // Top Navigation
            // -----------------
            Vue("div", { class: "w-full bg-white flex items-center justify-between px-6 py-3 border-b border-gray-200 shadow-sm sticky top-0 z-40" }, [
                Vue("div", { class: "flex items-center gap-4" }, [
                    Vue("button", {
                        onClick: toggleSidebar,
                        class: "flex flex-col justify-between w-6 h-5 focus:outline-none hover:opacity-80 transition"
                    }, [
                        Vue("span", { class: "block h-0.5 bg-gray-800 rounded" }),
                        Vue("span", { class: "block h-0.5 bg-gray-800 rounded" }),
                        Vue("span", { class: "block h-0.5 bg-gray-800 rounded" })
                    ]),
                    Vue("img", { src: "/images/washwiselogo2.png", alt: "WashWise Logo", class: "h-10 w-auto select-none", draggable: "false" })
                ]),
                Vue("div", { class: "flex items-center space-x-2 px-3 py-2 rounded-lg" }, [
                    Vue("span", { class: "text-gray-700 font-medium" }, user.value ? user.value.name : "Owner")
                ])
            ]),

            // -----------------
            // Sidebar
            // -----------------
            Vue("aside", {
                class: [
                    "fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-[#182235] to-[#0f172a] text-white shadow-lg z-50 transform transition-transform duration-300",
                    sidebarOpen.value ? "translate-x-0" : "-translate-x-full"
                ]
            }, [
                Vue("div", { class: "flex justify-between items-center p-4 border-b border-gray-700" }, [
                    Vue("h2", { class: "text-lg font-bold" }, "Menu"),
                    Vue("button", {
                        onClick: toggleSidebar,
                        class: "text-gray-400 hover:text-red-500 text-2xl"
                    }, "×")
                ]),

                Vue("nav", { class: "flex-1 mt-6 px-4 space-y-2" }, [
                    Vue(Link, {
                        href: route("owner.appointments"),
                        class: "group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-300 " +
                               (route().current("owner.appointments")
                               ? "bg-white text-[#182235] shadow-lg"
                               : "bg-gradient-to-r from-[#1F3A5F] to-[#162B4A] text-white hover:bg-white hover:text-[#182235] hover:shadow-md")
                    }, { default: () => "Appointments" }),

                    Vue(Link, {
                        href: route("owner.reviews"),
                        class: "group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-300 " +
                               (route().current("owner.reviews")
                               ? "bg-white text-[#182235] shadow-lg"
                               : "bg-gradient-to-r from-[#1F3A5F] to-[#162B4A] text-white hover:bg-white hover:text-[#182235] hover:shadow-md")
                    }, { default: () => "Reviews" }),

                    Vue(Link, {
                        href: route("owner.walkin"),
                        class: "group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-300 " +
                               (route().current("owner.walkin")
                               ? "bg-white text-[#182235] shadow-lg"
                               : "bg-gradient-to-r from-[#1F3A5F] to-[#162B4A] text-white hover:bg-white hover:text-[#182235] hover:shadow-md")
                    }, { default: () => "Walk-in" }),

                    // ✅ Injected Shop Toggle
                    shop.value ? Vue("div", { class: "mt-6 px-4" }, [
                        Vue("p", { class: "text-sm mb-2" }, [
                            "Shop Status: ",
                            Vue("span", {
                                class: shop.value.status === "open" ? "text-green-400" : "text-red-400"
                            }, shop.value.status)
                        ]),
                        shop.value.status === "open"
                            ? Vue("button", {
                                class: "w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700",
                                onClick: () => closeShop(shop.value.id)
                              }, "Close Shop")
                            : Vue("button", {
                                class: "w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700",
                                onClick: () => openShop(shop.value.id)
                              }, "Open Shop")
                    ]) : null,

                    Vue(Link, {
                        href: "/logout",
                        method: "post",
                        as: "button",
                        class: "group flex items-center gap-3 px-4 py-3 rounded-xl text-white font-medium transition-all duration-300 hover:bg-white hover:text-[#182235] hover:shadow-md"
                    }, { default: () => "Log Out" })
                ])
            ]),

            // -----------------
            // Hero Section
            // -----------------
            Vue("section", {
                class: "relative flex items-center justify-center min-h-screen bg-cover bg-center",
                style: { backgroundImage: "url('/images/hero-carwash.jpg')" }
            }, [
                Vue("div", { class: "absolute inset-0 bg-black/60" }),
                Vue("div", { class: "relative z-10 flex flex-col items-center justify-center w-full px-4 py-20" }, [
                    Vue("div", { class: "uppercase tracking-widest text-sm text-[#FF2D2D] font-bold mb-4" }, "Book Online"),
                    Vue("h1", { class: "text-4xl md:text-6xl font-extrabold text-white text-center mb-4 leading-tight drop-shadow" }, [
                        "Welcome, ",
                        Vue("span", { class: "text-4xl md:text-6xl font-extrabold text-white leading-tight drop-shadow" },
                            user.value ? user.value.name : "Owner"
                        ), "!"
                    ]),
                    Vue("div", { class: "text-base md:text-lg text-gray-100 text-center mb-8 max-w-2xl" },
                        "Your dashboard is ready and waiting. Let’s make today productive—review appointments, check feedback, and keep your business running smoothly."
                    ),
                    Vue(Link, {
                        href: route("owner.walkin"),
                        class: "px-8 py-3 rounded-full bg-[#FF2D2D] text-white font-bold text-lg shadow hover:bg-[#d72626] transition"
                    }, { default: () => "Walk-in" })
                ])
            ])
        ]);
    }
};
