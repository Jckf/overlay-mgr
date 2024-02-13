const template = document.querySelector("#itemCard");
const bidHistoryModal = document.querySelector("#bidHistoryModal");
const bidHistoryInfoTemplate = document.querySelector("#bidHistoryInfoTemplate");
const marketplaceTarget = document.querySelector(".appContainer main");
const bidInfoModal = document.querySelector("dialog#bidModal");
const bidHistoryMessages = bidHistoryModal.querySelector("#bidHistoryMessages");

fetch(`/api/items/`)
    .then(r => r.json())
    .then(items => populateSite(items))
    .catch(()=>{
        setTimeout(()=>{document.location.reload()}, 5000);
    });

mockedJson = {
    items: {
        "vase": {
            key: "vase",
            image: "/static/img/MingVase.webp",
            title: "300 bc vase, china",
            currentBid: 20013
        },
        "stearinlys": {
            key: "stearinlys",
            image: "/static/img/BundleOfCandles.webp",
            title: "Ancient bundle of candles, lightly used",
            currentBid: 1542
        }
    }
}

const populateSite = (auctionInventory) => {
    marketplaceTarget.innerHTML = "";
    auctionInventory.forEach(element => {
        let clone = template.content.cloneNode(true);

        clone.querySelector("h3").textContent  =	element.title;
        clone.querySelector(".bid").textContent =	(element.currentBid != null) ? `${element.currentBid},-`: '';
        clone.querySelector("img").src =			element.image;
        clone.querySelector("img").alt =			element.title.replace(`"`,`\"`);

        let button = clone.querySelector("button");
        button.addEventListener('click', (event) => {
            bidInfoModal.querySelector("#bidItem").textContent = `${element.title}`;
            bidInfoModal.querySelector("#bidMessage #bidMessageItemID").textContent = `${element.key}`
            bidInfoModal.showModal();
        });

        const bidHistory = clone.querySelector(".bidHistoryContainer a");

        bidHistory.addEventListener('click', (event) => {
            let history = element.bids;
            fetch(`/api/items/${element.id}/bids/`)
                .then(r => r.json())
                .then(items => {
                    bidHistoryMessages.innerHTML = "";
                    if (items == undefined || items.length == 0) {
                        let historyClone = document.createElement('div');
                        historyClone.textContent = 'Denne gjenstanden har ingen bud enda.';
                        bidHistoryMessages.appendChild(historyClone);

                        bidHistoryModal.showModal();
                        return;
                    }
                    items.forEach(historyItem => {
                        let historyClone = bidHistoryInfoTemplate.content.cloneNode(true);
                        let humanReadableDate = new Date(historyItem.timestamp).toDateString();
                        //let humanReadableDate = new Date(element.timestamp).toUTCString();

                        historyClone.querySelector(".bidHistoryInfoCard .bidTime").textContent = `${humanReadableDate}`;
                        historyClone.querySelector(".bidHistoryInfoCard .bidSender").textContent = `${historyItem.sender}`;
                        historyClone.querySelector(".bidHistoryInfoCard .bidMoney").textContent = `${historyItem.amount} NOK`;


                        bidHistoryMessages.appendChild(historyClone);
                    });

                    bidHistoryModal.showModal();
                })
                .catch(()=>{
                    bidHistoryMessages.innerHTML = "";
                    let historyClone = document.createElement('div');
                    historyClone.textContent = 'Denne gjenstanden har ingen bud enda.';
                    bidHistoryMessages.appendChild(historyClone);

                    bidHistoryModal.showModal();
                });


        });

        marketplaceTarget.appendChild(clone);
    });
}
