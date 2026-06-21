/**
 * Template Name: UBold - Admin & Dashboard Template
 * By (Author): Coderthemes
 * Module/App (File Name): Datatables Checkbox Select
 */

import DataTable from "datatables.net"
import "datatables.net-bs5/js/dataTables.bootstrap5.min.js"
import "datatables.net-responsive/js/dataTables.responsive.min.js"
import "datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"
import "datatables.net-select/js/dataTables.select.min.js"
import "datatables.net-select-bs5/js/select.bootstrap5.min.js"

document.addEventListener("DOMContentLoaded", () => {
    const tableElement = document.getElementById("checkbox-select-data")
    if (tableElement) {
        new DataTable(tableElement, {
            columnDefs: [
                {
                    orderable: false,
                    render: DataTable.render.select(),
                    targets: 0,
                },
            ],
            language: {
                paginate: {
                    first: '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11 7l-5 5l5 5" /><path d="M17 7l-5 5l5 5" /></svg>',
                    previous:
                        '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>',
                    next: '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>',
                    last: '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7l5 5l-5 5" /><path d="M13 7l5 5l-5 5" /></svg>',
                },
            },
            select: {
                style: "multi",
                selector: "td:first-child",
            },
            order: [[1, "asc"]],
        })
    }
})
