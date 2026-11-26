# Public restaurant data snapshot

`DatabaseSeeder` loads a small, repeatable snapshot of real Ho Chi Minh City branches and publicly listed menu items. Branch phone/address and item prices are public catalog facts, not a live feed. Checked on 2026-11-26.

## Branches

- KFC Lê Lai, 78 Lê Lai, Phường Bến Thành, Quận 1. KFC Vietnam careers listing: https://tuyendung.kfcvietnam.com.vn/danh-sach-tuyen-dung?pagenumber=8
- Jollibee Pasteur, 194D Pasteur, Phường Xuân Hòa. Jollibee Vietnam store directory (branch code JBVN003; phone 02838237000): https://help.jollibee.com.vn/hc/vi/articles/14378093877775-Danh-s%C3%A1ch-c%E1%BB%ADa-h%C3%A0ng
- Lotteria HTV, 11-11Bis Nguyễn Thị Minh Khai, Phường Bến Nghé, Quận 1. Lotteria Vietnam store directory (phone 02839101633): https://www.lotteria.vn/danh-sach-so-dien-thoai-cua-hang-LOTTERIA

## Menu and prices

- KFC prices and names: KFC Vietnam ordering menu, https://kfcvietnam.com.vn/order/delivery/hot-deal
- Jollibee prices and names: Jollibee Vietnam Help Center menu, updated 2026-09-11, https://help.jollibee.com.vn/hc/vi/articles/17575951266831-T%C3%B4i-c%C3%B3-th%E1%BB%83-g%E1%BB%8Di-m%C3%B3n-g%C3%AC-t%E1%BB%AB-th%E1%BB%B1c-%C4%91%C6%A1n
- Lotteria branch menu and prices: beFood listing for Lotteria HTV, https://food.be.com.vn/ho-chi-minh/lotteria-htv-11613. This is the delivery marketplace’s public listing for the branch, not a Lotteria API.

The hub `capacity` value of 20 is an initial SaigonBite queue limit chosen for this application. It is not a restaurant-reported capacity. Change it to an agreed operating limit before using the app with real customers.

Orders created by this project are saved to its own database. The app does not submit orders to KFC, Jollibee, Lotteria, or their POS systems, and it does not receive live inventory, operating status, or capacity data. This dataset makes the local ordering flow runnable with real public catalog facts; it does not make the project an authorized live ordering integration.
