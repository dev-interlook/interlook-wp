<?php



/**



 Template Name: Boooking



 */





get_header(); ?>
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <!-- Content -->
  <div class="content-wrapper">
    <?php if ( have_rows( 'banner' ) ) : ?>
      <?php while ( have_rows( 'banner' ) ) : the_row(); ?>
        <!-- Lines -->
        <section class="content-lines-wrapper">
            <div class="content-lines-inner">
                <div class="content-lines"></div>
            </div>
        </section>
        <!-- Header Banner -->
        <section class="banner-header banner-img valign bg-img bg-fixed"
          style="background-position: center;"
          data-overlay-darkgray="5"
          data-background="<?php the_sub_field( 'background' ); ?>">
        </section>
      <?php endwhile; ?>
    <?php endif; ?>
    <!-- Blog  -->
    <section class="bauen-blog3 section-padding2">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<br>
						<h2 class="section-title"><?php the_sub_field( 'title_page' ); ?></h2>
          </div>
				</div>
				<div class="row">
					<div class="col-md-12">
            <form class="static-booking-form row">
              <div class="form-group col-md-12">
                <label for="kodePromo">Kode Promo</label>
                <input type="text" id="kodePromo" aria-describedby="kodePromoHelp">
                <small id="kodePromoHelp" class="form-text text-muted">Kosongkan saja jika tidak memiliki kode promo.</small>
              </div>
              <div class="form-group col-md-12">
                <label for="namaLengkap">Nama Lengkap <span class="required">*</span></label>
                <input type="text" id="namaLengkap" aria-describedby="namaLengkapHelp">
              </div>
              <div class="form-group col-md-6">
                <label for="email">Email <span class="required">*</span></label>
                <input type="text" id="email" aria-describedby="emailHelp">
              </div>
              <div class="form-group col-md-6">
                <label for="nomorTelepon">Nomor Telepon</label>
                <input type="text" id="nomorTelepon" aria-describedby="nomorTeleponHelp">
              </div>
              <div class="form-group col-md-12">
                <label for="alamatProject">Alamat Project <span class="required">*</span></label>
                <textarea id="alamatProject" aria-describedby="alamatProjectHelp"></textarea>
              </div>
              <div class="form-group col-md-12">
                <label for="luasTanah">Luas Tanah <span class="required">*</span></label>
                <input type="text" id="luasTanah" aria-describedby="luasTanahHelp">
              </div>
              <div class="form-group col-md-12">
                <label for="luasBangunan">Luas Bangunan Saat Ini [Eksisting] <span class="required">*</span></label>
                <input type="text" id="luasBangunan" aria-describedby="luasBangunanHelp">
              </div>
              <div class="form-group col-md-6">
                <label for="fotoEksisting">Foto Saat Ini [Eksisting] <span class="required">*</span></label>
                <input type="file" id="fotoEksisting" aria-describedby="fotoEksistingHelp">
              </div>
              <div class="form-group col-md-6">
                <label for="referensiDesain">Referensi Desain <span class="required">*</span></label>
                <input type="file" id="referensiDesain" aria-describedby="referensiDesainHelp">
              </div>
              <div class="form-group col-md-12">
                <label for="permasalahanKebutuhan">Permasalahan & Kebutuhan Desain</label>
                <textarea id="permasalahanKebutuhan" aria-describedby="permasalahanKebutuhanHelp"></textarea>
              </div>
              <div class="form-group col-md-12">
                <label for="budgetPembangunan">Budget Pembangunan <span class="required">*</span></label>
                <input type="number" id="budgetPembangunan" aria-describedby="budgetPembangunanHelp">
              </div>

              <div class="col-md-12 d-flex justify-content-end">
                <button type="submit" class="btn">Booking Sekarang!</button>
              </div>
            </form>
				  </div>
        </div>
      </div>
    </section>

    <div id="loading" class="loading-overlay d-flex justify-content-center align-items-center">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Get the query parameter 'code' from the URL
      const urlParams = new URLSearchParams(window.location.search);
      const codeValue = urlParams.get("code");

      // Check if 'code' exists and set it to the input field
      if (codeValue) {
          const inputField = document.getElementById("kodePromo");
          if (inputField) {
              inputField.value = codeValue;
          }
      }
    });

    document.querySelector('.static-booking-form').addEventListener('submit', async (e) => {
      e.preventDefault();

      // Check required fields
      const requiredFields = {
        'namaLengkap': 'Nama Lengkap',
        'email': 'Email',
        'alamatProject': 'Alamat Project', 
        'luasTanah': 'Luas Tanah',
        'luasBangunan': 'Luas Bangunan',
        'fotoEksisting': 'Foto Eksisting',
        'referensiDesain': 'Referensi Desain',
        'budgetPembangunan': 'Budget Pembangunan'
      };

      for (const [fieldId, fieldName] of Object.entries(requiredFields)) {
        const field = document.getElementById(fieldId);
        if (fieldId.includes('foto') || fieldId.includes('referensi')) {
          if (!field.files[0]) {
            alert(`${fieldName} harus diisi!`);
            return;
          }
        } else if (!field.value.trim()) {
          alert(`${fieldName} harus diisi!`);
          return;
        }
      }

      const loading = document.getElementById('loading');
      loading.style.visibility = 'visible';
      document.body.style.pointerEvents = 'none';
      
      const formData = new FormData();
      formData.append('kode_promo', document.getElementById('kodePromo').value);
      formData.append('nama_lengkap', document.getElementById('namaLengkap').value);
      formData.append('email', document.getElementById('email').value);
      formData.append('nomor_telepon', document.getElementById('nomorTelepon').value);
      formData.append('alamat_project', document.getElementById('alamatProject').value);
      formData.append('luas_tanah', document.getElementById('luasTanah').value);
      formData.append('luas_bangunan', document.getElementById('luasBangunan').value);
      formData.append('foto_eksisting', document.getElementById('fotoEksisting').files[0]);
      formData.append('referensi_desain', document.getElementById('referensiDesain').files[0]);
      formData.append('permasalahan_kebutuhan', document.getElementById('permasalahanKebutuhan').value);
      formData.append('budget_pembangunan', document.getElementById('budgetPembangunan').value);

      try {
        const response = await fetch('https://mailgun-webhook.interlook.co.id/send-email', {
          method: 'POST',
          body: formData
        });

        if (response.ok) {
          // hide the loading overlay
          loading.style.visibility = 'hidden';
          document.body.style.pointerEvents = 'auto';
          // redirect to the booking success page
          window.location.href = '/booking-success';
        } else {
          throw new Error('Failed to submit form');
        }
      } catch (error) {
        console.error('Error:', error);
      }
    });
  </script>

<?php get_footer();?>