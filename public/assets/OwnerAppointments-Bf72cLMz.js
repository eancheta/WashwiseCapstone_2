import {
  d as defineComponent,
  r as ref,
  b as computed,
  c as n,
  o as a,
  l as A,
  a as t,
  u as F,
  m as M,
  e as g,
  g as xfunc,
  h as S,
  i as T,
  v as h,
  F as _,
  j as V,
  W as router,
  n as W,
  t as r
} from "./app-p4oQ-QsI.js";

const E = { class: "min-h-screen bg-gradient-to-br from-gray-100 via-white to-gray-100 py-6 px-4 flex flex-col items-center" },
      I = { class: "w-full max-w-7xl p-6 sm:p-10" },
      P = { class: "mb-8 p-6 bg-white shadow-lg rounded-2xl grid grid-cols-1 sm:grid-cols-3 gap-4 border border-gray-200" },
      j = { key: 0 },
      $ = { key: 1 },
      z = { key: 0, class: "text-gray-500 text-lg text-center py-12" },
      R = { key: 1, class: "overflow-x-auto" },
      U = { class: "w-full border border-gray-200 rounded-xl overflow-hidden shadow-sm" },
      L = { class: "text-[#182235] font-medium" },
      O = { class: "px-4 py-3" },
      Y = { class: "px-4 py-3" },
      H = { class: "px-4 py-3" },
      q = { key: 0, class: "text-green-600 font-semibold" },
      G = { key: 1, class: "text-red-600 font-semibold" },
      J = { key: 2, class: "text-blue-600 font-semibold" },
      K = { key: 3, class: "text-gray-600 font-semibold" },
      Q = { class: "px-4 py-3 text-center" },
      X = { class: "px-4 py-3" },
      Z = { class: "px-4 py-3" },
      tt = { class: "px-4 py-3" },
      et = { class: "px-4 py-3" },
      ot = { class: "px-4 py-3" },
      st = { class: "px-4 py-3 text-center" },
      nt = ["src"],
      at = { key: 1, class: "text-gray-400 italic" },
      lt = { class: "px-4 py-3 text-center font-bold text-[#FF2D2D]" },
      rt = { class: "px-4 py-3 text-center flex justify-center gap-3" },
      it = ["onClick"],
      dt = ["onClick"];

// Use same base as Dashboard so rendering matches exactly
const baseURL = "http://127.0.0.1:8000";

const pt = defineComponent({
  __name: "OwnerAppointments",
  props: { appointments: {} },
  setup(b) {
    const v = b;

    function approve(id) {
      router.post(`/owner/appointments/${id}/approve`);
    }
    function decline(id) {
      router.post(`/owner/appointments/${id}/decline`);
    }

    // error handler: swap to fallback asset under same base
    function handleImgError(event) {
      try {
        const img = event && event.target;
        if (img && img.tagName === "IMG") {
          img.onerror = null; // avoid infinite loop
          img.src = `${baseURL}/images/no-proof.png`;
        }
      } catch (e) {
        // silent
      }
    }

    // Build src to mimic Dashboard rendering: use baseURL + /storage/<file>
    function getPaymentProofSrc(appt) {
      if (!appt.payment_proof) return null;
      // if already a full url, return it
      if (appt.payment_proof.startsWith("http")) {
        return appt.payment_proof;
      }
      // otherwise assume it's stored in /storage/ (like Dashboard)
      return `${baseURL}/storage/${appt.payment_proof}`;
    }

    const dateRange = ref("all"),
          fromDate = ref(""),
          toDate = ref(""),
          filtered = computed(() => {
            let list = [...v.appointments];
            const today = new Date();
            let start = null, end = null;

            if (dateRange.value === "today") {
              start = new Date(today.toDateString());
              end = new Date(start); end.setDate(end.getDate() + 1);
            } else if (dateRange.value === "week") {
              const firstDay = today.getDate() - today.getDay();
              start = new Date(today.setDate(firstDay));
              end = new Date(today.setDate(firstDay + 7));
            } else if (dateRange.value === "month") {
              start = new Date(today.getFullYear(), today.getMonth(), 1);
              end = new Date(today.getFullYear(), today.getMonth() + 1, 1);
            } else if (dateRange.value === "custom" && fromDate.value && toDate.value) {
              start = new Date(fromDate.value);
              end = new Date(toDate.value); end.setDate(end.getDate() + 1);
            }

            if (start && end) {
              list = list.filter(a => {
                const d = new Date(a.date_of_booking);
                return d >= start && d < end;
              });
            }
            return list;
          });

    function goBack() {
      router.visit("/owner/dashboard");
    }

    return (ctx, cache) => (
      a(),
      n(_, null, [
        A(F(M), { title: "Owner Appointments" }),
        t("div", E, [
          t("div", I, [
            cache[8] || (cache[8] = t("div", { class: "text-center mb-8" }, [
              t("h1", { class: "text-3xl sm:text-4xl font-extrabold text-[#002B5C] mb-2" }, " Customer Appointments "),
              t("p", { class: "text-gray-500" }, "Manage and monitor all customer bookings efficiently")
            ], -1)),
            t("div", { class: "mb-6 flex justify-start" }, [
              t("button", { onClick: goBack, class: "px-6 py-3 bg-[#002B5C] text-white font-semibold rounded-xl shadow-md hover:bg-[#FF2D2D] transition transform hover:-translate-y-0.5" }, " ← Return to Dashboard ")
            ]),
            t("div", P, [
              t("div", null, [
                cache[4] || (cache[4] = t("label", { class: "block text-sm font-semibold text-gray-600 mb-1" }, "Date Range", -1)),
                xfunc(t("select", { "onUpdate:modelValue": cache[0] || (cache[0] = v => dateRange.value = v), class: "w-full px-4 py-3 font-semibold text-[#182235] border rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:outline-none" }, [
                  T('<option value="all">All</option><option value="today">Today</option><option value="week">This Week</option><option value="month">This Month</option><option value="custom">Custom</option>', 5)
                ], 512), [[S, dateRange.value]])
              ]),
              dateRange.value === "custom" ? (a(), n("div", j, [
                cache[5] || (cache[5] = t("label", { class: "block text-sm font-semibold text-gray-600 mb-1" }, "From", -1)),
                xfunc(t("input", { type: "date", "onUpdate:modelValue": cache[1] || (cache[1] = v => fromDate.value = v), class: "w-full px-4 py-3 font-semibold text-[#182235] border rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:outline-none" }, null, 512), [[h, fromDate.value]])
              ])) : g("", !0),
              dateRange.value === "custom" ? (a(), n("div", $, [
                cache[6] || (cache[6] = t("label", { class: "block text-sm font-semibold text-gray-600 mb-1" }, "To", -1)),
                xfunc(t("input", { type: "date", "onUpdate:modelValue": cache[2] || (cache[2] = v => toDate.value = v), class: "w-full px-4 py-3 font-semibold text-[#182235] border rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:outline-none" }, null, 512), [[h, toDate.value]])
              ])) : g("", !0)
            ]),
            filtered.value.length === 0 ? (a(), n("div", z, " No appointments found. ")) : (a(), n("div", R, [
              t("table", U, [
                cache[7] || (cache[7] = t("thead", { class: "bg-gradient-to-r from-[#002B5C] to-[#00509E] text-white" }, [
                  t("tr", null, [
                    t("th", { class: "px-4 py-3 text-left" }, "Date"),
                    t("th", { class: "px-4 py-3 text-left" }, "Time"),
                    t("th", { class: "px-4 py-3 text-left" }, "Status"),
                    t("th", { class: "px-4 py-3 text-center" }, "ID"),
                    t("th", { class: "px-4 py-3 text-left" }, "Name"),
                    t("th", { class: "px-4 py-3 text-left" }, "Email"),
                    t("th", { class: "px-4 py-3 text-left" }, "Car Size"),
                    t("th", { class: "px-4 py-3 text-left" }, "Contact"),
                    t("th", { class: "px-4 py-3 text-left" }, "Slot"),
                    t("th", { class: "px-4 py-3 text-left" }, "Created"),
                    t("th", { class: "px-4 py-3 text-center" }, "Payment Proof"),
                    t("th", { class: "px-4 py-3 text-center" }, "Amount"),
                    t("th", { class: "px-4 py-3 text-center" }, "Actions")
                  ])
                ], -1)),
                t("tbody", L, [
                  (a(!0), n(_, null, V(filtered.value, (e, s) => (
                    a(),
                    n("tr", { key: e.id, class: W(s % 2 === 0 ? "bg-white hover:bg-gray-50" : "bg-gray-50 hover:bg-gray-100 transition") }, [
                      t("td", O, r(e.date_of_booking), 1),
                      t("td", Y, r(e.time_of_booking), 1),
                      t("td", H, [
                        e.status === "approved"
                          ? (a(), n("span", q, "Approved"))
                          : e.status === "declined"
                          ? (a(), n("span", G, "Declined"))
                          : e.status === "paid"
                          ? (a(), n("span", J, "Paid"))
                          : (a(), n("span", K, "Pending"))
                      ]),
                      t("td", Q, r(e.id), 1),
                      t("td", X, r(e.name), 1),
                      t("td", Z, r(e.email || "Walk_IN"), 1),
                      t("td", tt, r(e.size_of_the_car), 1),
                      t("td", et, r(e.contact_no), 1),
                      t("td", ot, r(e.slot_number), 1),
                      t("td", O, r(e.created_at), 1),
                      t("td", st, [
                        e.payment_proof
                          ? (a(), n("img", {
                              key: 0,
                              src: getPaymentProofSrc(e),
                              alt: "Payment Proof",
                              class: "h-16 w-16 object-cover rounded border mx-auto",
                              onError: handleImgError
                            }, null, 40, nt))
                          : (a(), n("span", at, "Walk_IN"))
                      ]),
                      t("td", lt, r(e.payment_amount ?? "Walk_IN"), 1),
                      t("td", rt, [
                        t("button", {
                          onClick: d => approve(e.id),
                          class: "px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 shadow-md transition transform hover:-translate-y-0.5"
                        }, " Approve ", 8, it),
                        t("button", {
                          onClick: d => decline(e.id),
                          class: "px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 shadow-md transition transform hover:-translate-y-0.5"
                        }, " Decline ", 8, dt)
                      ])
                    ], 2)
                  )), 128))
                ])
              ])
            ]))
          ])
        ])
      ], 64)
    );
  }
});

export { pt as default };
