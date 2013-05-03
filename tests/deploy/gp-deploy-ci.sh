# Make a backup of the current install!
ssh -i /home/jduquette/scripts/grabdev1 bitnami@grabpress-ci.grab-media.com "cp -R /opt/bitnami/apps/wordpress/htdocs/wp-content/plugins/grabpress /opt/bitnami/apps/wordpress/htdocs/wp-content/plugins/backups/grabpress-BACKUP"

# Remove the old build
ssh -i /home/jduquette/scripts/grabdev1 bitnami@grabpress-ci.grab-media.com "rm -rf /opt/bitnami/apps/wordpress/htdocs/wp-content/plugins/grabpress"

# Upload src as grabpress to the remote machine:
scp -i /home/jduquette/scripts/grabdev1 -r /var/atlassian/application-data/bamboo/xml-data/build-dir/GBPS-GBPS-JOB1/src bitnami@grabpress-ci.grab-media.com:/opt/bitnami/apps/wordpress/htdocs/wp-content/plugins/grabpress

