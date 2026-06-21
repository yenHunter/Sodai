/**
 * Template Name: UBold - Admin & Dashboard Template
 * By (Author): Coderthemes
 * Module/App (File Name): Chartjs Bar
 */

import { CustomChartJs, theme } from "../app"

const bodyFont = getComputedStyle(document.body).fontFamily.trim()

new CustomChartJs({
    selector: "#basic-bar-chart",
    options: () => {
        return {
            type: "bar",
            data: {
                labels: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"],
                datasets: [
                    {
                        data: [4, 4, 5, 6, 8, 5, 4, 6, 8, 5],
                        backgroundColor: theme("chart-primary"),
                        borderRadius: 4,
                        borderSkipped: false,
                    },
                ],
            },
            options: {
                interaction: {
                    mode: "index",
                    intersect: false,
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        mode: "index",
                        intersect: false,
                    },
                },
            },
        }
    },
})

new CustomChartJs({
    selector: "#border-radius-bar-chart",
    options: () => {
        return {
            type: "bar",
            data: {
                labels: ["Jan", "Feb", "March", "April", "May", "June"],
                datasets: [
                    {
                        label: "Fully Rounded",
                        data: [12, -19, 14, -15, 12, -14],
                        borderColor: theme("chart-gray"),
                        backgroundColor: theme("chart-gray", 0.2),
                        borderWidth: 2,
                        borderRadius: Number.MAX_VALUE,
                        borderSkipped: false,
                    },
                    {
                        label: "Small Radius",
                        data: [-10, 19, -15, -8, 12, -7],
                        borderColor: theme("chart-primary"),
                        backgroundColor: theme("chart-primary", 0.2),
                        borderWidth: 2,
                        borderRadius: 5,
                        borderSkipped: false,
                    },
                ],
            },
            options: {
                interaction: {
                    mode: "index",
                    intersect: false,
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        mode: "index",
                        intersect: false,
                    },
                },
            },
        }
    },
})

new CustomChartJs({
    selector: "#floating-bar-chart",
    options: () => ({
        type: "bar",
        data: {
            labels: ["Jan", "Feb", "March", "April", "May", "June"],
            datasets: [
                {
                    label: "Fully Rounded",
                    data: [12, -19, 14, -15, 12, -14],
                    backgroundColor: theme("chart-primary"),
                },
                {
                    label: "Small Radius",
                    data: [-10, 19, -15, -8, 12, -7],
                    backgroundColor: theme("chart-gray"),
                },
            ],
        },
        options: {
            interaction: {
                mode: "index",
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: "top",
                    labels: {
                        font: { family: bodyFont },
                        color: theme("secondary-color"),
                        usePointStyle: true, // Show circles instead of default box
                        pointStyle: "circle", // Circle shape
                        boxWidth: 8, // Circle size
                        boxHeight: 8, // (optional) same as width by default
                        padding: 15, // Space between legend items
                    },
                },
                tooltip: {
                    enabled: true,
                    titleFont: { family: bodyFont },
                    bodyFont: { family: bodyFont },
                },
            },
        },
    }),
})

new CustomChartJs({
    selector: "#horizontal-bar-chart",
    options: () => ({
        type: "bar",
        data: {
            labels: ["Jan", "Feb", "March", "April"],
            datasets: [
                {
                    label: "Fully Rounded",
                    data: [12, -19, 14, -15],
                    borderColor: theme("chart-gray"),
                    backgroundColor: theme("chart-gray", 0.2),
                    borderWidth: 1,
                },
                {
                    label: "Small Radius",
                    data: [-10, 19, -15, -8],
                    borderColor: theme("chart-primary"),
                    backgroundColor: theme("chart-primary", 0.2),
                    borderWidth: 1,
                },
            ],
        },
        options: {
            indexAxis: "y",
            elements: {
                bar: {
                    borderWidth: 2,
                },
            },
            interaction: {
                mode: "index",
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: "top",
                    labels: {
                        font: { family: bodyFont },
                        color: theme("secondary-color"),
                        usePointStyle: true,
                        pointStyle: "circle",
                        boxWidth: 8,
                        boxHeight: 8,
                        padding: 15,
                    },
                },
                tooltip: {
                    enabled: true,
                    titleFont: { family: bodyFont },
                    bodyFont: { family: bodyFont },
                },
            },
        },
    }),
})

new CustomChartJs({
    selector: "#stacked-bar-chart",
    options: () => ({
        type: "bar",
        data: {
            labels: ["Jan", "Feb", "March", "April", "May"],
            datasets: [
                {
                    label: "Dataset 1",
                    data: [12, -19, 14, -15, 8, 10],
                    backgroundColor: theme("chart-gray"),
                },
                {
                    label: "Dataset 2",
                    data: [-10, 19, -15, -8, 12, 6],
                    backgroundColor: theme("chart-secondary"),
                },
                {
                    label: "Dataset 3",
                    data: [-5, 14, -10, -12, 7, 4],
                    backgroundColor: theme("chart-primary"),
                },
                {
                    label: "Dataset 4",
                    data: [8, -12, 10, -6, 15, -3],
                    backgroundColor: theme("chart-beta"),
                },
            ],
        },
        options: {
            scales: {
                x: {
                    stacked: true,
                    ticks: {
                        font: { family: bodyFont },
                        color: theme("secondary-color"),
                        display: true,
                        drawTicks: true,
                    },
                    grid: {
                        display: false,
                        drawBorder: false,
                    },
                    border: {
                        display: false, // Hides bottom X axis line
                    },
                },
                y: {
                    stacked: true,
                    ticks: {
                        font: { family: bodyFont },
                        color: theme("secondary-color"),
                    },
                    grid: {
                        display: true, // Keeps horizontal lines
                        drawBorder: false, // Hides Y axis border line
                        color: theme("chart-border-color"),
                        lineWidth: 1,
                    },
                    border: {
                        display: false, // Hides Y axis line (left)
                        dash: [5, 5],
                    },
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
            },
        },
    }),
})

new CustomChartJs({
    selector: "#stacked-groups-bar-chart",
    options: () => ({
        type: "bar",
        data: {
            labels: ["Jan", "Feb", "March", "April"],
            datasets: [
                {
                    label: "Dataset 1",
                    data: [12, -19, 14, -15],
                    backgroundColor: theme("chart-gamma"),
                    stack: "Stack 0",
                },
                {
                    label: "Dataset 2",
                    data: [-10, 19, -15, -8],
                    backgroundColor: theme("chart-primary"),
                    stack: "Stack 0",
                },
                {
                    label: "Dataset 3",
                    data: [-10, 19, -15, -8],
                    backgroundColor: theme("chart-gray"),
                    stack: "Stack 1",
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false,
                },
            },
        },
    }),
})

new CustomChartJs({
    selector: "#vertical-bar-chart",
    options: () => ({
        type: "bar",
        data: {
            labels: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"],
            datasets: [
                {
                    label: "Dataset 1",
                    data: [4, 4, 5, 6, 8, 5, 4, 6, 8, 5],
                    backgroundColor: theme("chart-gamma"),
                    borderRadius: 4,
                    barThickness: 8,
                },
                {
                    label: "Dataset 2",
                    data: [3, 5, 4, 7, 6, 5, 6, 7, 5, 4],
                    backgroundColor: theme("chart-primary"),
                    borderRadius: 4,
                    barThickness: 8,
                },
                {
                    label: "Dataset 3",
                    data: [5, 3, 6, 4, 7, 6, 5, 4, 6, 7],
                    backgroundColor: theme("chart-secondary"),
                    borderRadius: 4,
                    barThickness: 8,
                },
            ],
        },
    }),
})
