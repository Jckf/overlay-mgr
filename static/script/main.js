const itemCardTemplate = document.querySelector("#itemCard");
const bidHistoryModal = document.querySelector("#bidHistoryModal");
const bidHistoryInfoTemplate = document.querySelector("#bidHistoryInfoTemplate");
const marketplaceTarget = document.querySelector("main #items");
const bidInfoModal = document.querySelector("#bidModal");
const bidHistoryMessages = bidHistoryModal.querySelector("#bidHistoryMessages");

const numberFormat = new Intl.NumberFormat('nb-NO');

const updateBids = () => {
    fetch('/api/items/?limit=999')
        .then(response => response.json())
        .then(items => populateSite(items))
        .then(() => setTimeout(updateBids, 15 * 1000))
        .catch(() => {
            setTimeout(() => document.location.reload(), 5000);
        });
};

updateBids();

const populateSite = (auctionInventory) => {
    marketplaceTarget.innerHTML = '';

    auctionInventory.forEach(item => {
        let itemCard = itemCardTemplate.content.cloneNode(true);

        itemCard.querySelector('img').src = item.image;
        itemCard.querySelector('.title').textContent = item.title;
        itemCard.querySelector('.description').textContent = item.description;
        itemCard.querySelector('.key').textContent = item.key;
        itemCard.querySelector('.bid').textContent = (item.currentBid !== null) ? numberFormat.format(item.currentBid) + ',-': '';

        let bidButton;
        if (bidButton = itemCard.querySelector('button')) {
            bidButton.addEventListener('click', () => {
                bidInfoModal.querySelector('#bidItem').textContent = item.title;
                bidInfoModal.querySelector('#bidMessage #bidMessageItemID').textContent = item.key;
                bidInfoModal.showModal();
            });
        }

        let historyButton;
        if (historyButton = itemCard.querySelector('.historyButton')) {
            historyButton.addEventListener('click', () => {
                fetch(`/api/items/${item.id}/bids/`)
                    .then(response => response.json())
                    .then(items => {
                        bidHistoryMessages.innerHTML = '';

                        if (items === undefined || items.length === 0) {
                            let historyClone = document.createElement('div');
                            historyClone.textContent = 'Denne gjenstanden har ingen bud enda.';
                            bidHistoryMessages.appendChild(historyClone);
                            bidHistoryModal.showModal();
                            return;
                        }

                        items.forEach(bid => {
                            let historyClone = bidHistoryInfoTemplate.content.cloneNode(true);

                            const ts = new Date(bid.timestamp);
                            const humanReadableDate = ts.toLocaleDateString();
                            const humanReadableTime = ts.toLocaleTimeString();

                            historyClone.querySelector(".bidHistoryInfoCard .bidTime").textContent = humanReadableDate + ' ' + humanReadableTime;
                            historyClone.querySelector(".bidHistoryInfoCard .bidSender").textContent = bid.sender;
                            historyClone.querySelector(".bidHistoryInfoCard .bidMoney").textContent = numberFormat.format(bid.amount) + ',-';


                            bidHistoryMessages.appendChild(historyClone);
                        });

                        bidHistoryModal.showModal();
                    })
                    .catch(() => {
                        bidHistoryMessages.innerHTML = '';
                        let historyClone = document.createElement('div');
                        historyClone.textContent = 'Denne gjenstanden har ingen bud enda.';
                        bidHistoryMessages.appendChild(historyClone);

                        bidHistoryModal.showModal();
                    });
            });
        }

        marketplaceTarget.appendChild(itemCard);
    });
}
