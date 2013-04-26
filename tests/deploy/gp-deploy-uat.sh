# Make a backup of the current install!
ssh -i /home/jduquette/scripts/grabdev1 bitnami@grabpress.grabstaging.com "cp -R /opt/bitnami/apps/wordpress/htdocs/wp-content/plugins/grabpress /opt/bitnami/apps/wordpress/htdocs/wp-content/plugins/backups/grabpress-BACKUP" 

# Remove the old build
ssh -i /home/jduquette/scripts/grabdev1 bitnami@grabpress.grabstaging.com "rm -rf /opt/bitnami/apps/wordpress/htdocs/wp-content/plugins/grabpress"

# Upload src as grabpress to the remote machine:
scp -i /home/jduquette/scripts/grabdev1 -r /var/atlassian/application-data/bamboo/xml-data/build-dir/GBPS-GBPS-JOB1/src bitnami@grabpress.grabstaging.com:/opt/bitnami/apps/wordpress/htdocs/wp-content/plugins/grabpress

echo "***********************************************************************************"
echo "   CODE HAS BEEN DEPLOYED TO USER ACCEPTANCE TEST SITE"
echo "   http://grabpress.grabstaging.com/wordpress/wp-admin/admin.php?page=gp-dashboard"
echo "   username: user password: bitnami"
echo "***********************************************************************************"
