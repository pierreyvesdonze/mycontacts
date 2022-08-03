window.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById('myChart');
    const myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [
                'Abstinence',
                'Bière',
                'Vin',
                'Alcool fort'
            ],
            datasets: [{
                label: 'Répartition des alcools',
                data: [
                    document.getElementById("xSober").dataset.sober,
                    document.getElementById("xBeer").dataset.beer,
                    document.getElementById('xWine').dataset.wine,
                    document.getElementById('xSpiritus').dataset.spiritus
                ],
                backgroundColor: [
                    'green',
                    'rgb(255, 205, 86)',
                    'rgb(115, 18, 18, 1)',
                    'rgb(250, 122, 3, 1)'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        // This more specific font property overrides the global property
                        color: 'wheat',
                        font: {
                            size: 14,
                            position: 'center',
                        }
                    }
                }
            }
        }
    })

    const ctx2 = document.getElementById('myChart2');
    const myChart2 = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'],
            datasets: [{
                label: 'Tendances des jours de la semaine',
                data: [
                    document.getElementById("mondayDrinks").dataset.monday,
                    document.getElementById("tuesdayDrinks").dataset.tuesday,
                    document.getElementById("wednesdayDrinks").dataset.wednesday,
                    document.getElementById("thursdayDrinks").dataset.thursday,
                    document.getElementById("fridayDrinks").dataset.friday,
                    document.getElementById("saturdayDrinks").dataset.saturday,
                    document.getElementById("sundayDrinks").dataset.sunday
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    labels: {
                        // This more specific font property overrides the global property
                        color: 'wheat',
                        font: {
                            size: 14,
                            position: 'center',
                        }
                    }
                }
            }
        }
    });
});